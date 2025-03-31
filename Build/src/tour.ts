import DocumentService from "@macopedia/typo3-interactive-tour/typo3/document-service";
import {
    driver,
    type Driver,
    type DriveStep,
    type Config,
} from "./driver/driver";
import "./driver/driver.css";
import { MAIN_IFRAME_SELECTOR, ACTION_TYPES } from "./consts";
import { sleep } from "./helpers";
import { State } from "./driver/state";
import { PopoverDOM } from "./driver/popover";

export interface TourEvent {
    event: string;
    target: string;
    isDone: boolean;
    delayBefore?: number;
    delayAfter?: number;
    frame?: string;
}

export interface TourStep extends DriveStep {
    frame?: string;
    disableActiveInteraction?: boolean;
    requireUserActions?: boolean;
    onShow?: TourEvent[];
    onExit?: TourEvent[];
    userActions?: TourEvent[];
    skipButtonText?: string;
    sleep?: number;
}

export interface TourConfig {
    identifier: string;
    onTourCleanup?: (identifier?: string) => Promise<void>;
    onStepChange?: (identifier: string, stepIndex: number) => Promise<void>;
    onClose?: (identifier?: string) => Promise<void>;
    steps: TourStep[];
    driverConfig?: Config & { skipBtnText?: string };
    stepIndex?: number;
}

export default class Tour {
    identifier: string;
    config: TourConfig;
    rawSteps: TourConfig["steps"] = [];
    private driverInstance: Driver;
    private isInitialized: boolean = false;
    private resizeObserver: ResizeObserver | null = null;

    constructor(config: TourConfig) {
        this.config = config;
        const { identifier, steps, driverConfig, stepIndex = 0 } = this.config;
        this.identifier = identifier;

        const defaultDriverConfig: Config = {
            showProgress: true,
            animate: true,
            overlayColor: "#000000",
            overlayOpacity: 0.7,
            stagePadding: 0,
            stageRadius: 0,
            allowClose: false,
            showButtons: ["next", "previous"],
            disableActiveInteraction: true,
            allowKeyboardControl: true,
            nextBtnText: "Next",
            prevBtnText: "Back",
            doneBtnText: "Finish",
            onNextClick: this.onNextClick.bind(this),
            onPrevClick: this.onPrevClick.bind(this),
            onPopoverRender: this.onPopoverRender.bind(this) as any,
        };

        const mergedConfig: Config = {
            ...defaultDriverConfig,
            ...driverConfig,
        };

        this.driverInstance = driver(mergedConfig);

        this.rawSteps = steps || [];
        if (steps?.length) {
            this.initialize(steps, stepIndex);
        }
        this.eventsListener = this.eventsListener.bind(this);
    }

    private initialize(steps: TourStep[], stepIndex: number): void {
        const hasIframeSteps = steps.some((step) => step.frame);
        DocumentService.ready().then((): void => {
            if (hasIframeSteps) {
                const firstIframeStep = steps.find((step) => step.frame);
                let iframeSelectorCount = 0;
                const checkElement = async (): Promise<void> => {
                    const iframe = document.querySelector(
                        firstIframeStep?.frame || MAIN_IFRAME_SELECTOR
                    ) as HTMLIFrameElement | null;
                    if (
                        iframe &&
                        iframe.contentWindow?.document.readyState === "complete"
                    ) {
                        this.resizeObserver = new ResizeObserver(async () => {
                            const currentStep = this.getCurrentStepIndex();
                            const targetStepIndex = this.isInitialized
                                ? currentStep
                                : stepIndex;
                            const targetStep =
                                this.getRawStepByIndex(targetStepIndex);
                            if (targetStep?.sleep) {
                                await sleep(targetStep.sleep);
                            }

                            this.destroy();
                            this.setSteps(steps || []);
                            this.start(
                                this.isInitialized ? currentStep : stepIndex
                            );
                            this.isInitialized = true;
                        });
                        this.resizeObserver.observe(iframe);
                    } else {
                        iframeSelectorCount++;
                        if (iframeSelectorCount >= 10) {
                            console.error(
                                `Can't finde iframe for selector: "${firstIframeStep?.frame}"`
                            );
                        } else {
                            setTimeout(checkElement, 100);
                        }
                    }
                };
                checkElement();
            } else {
                this.setSteps(steps || []);
                this.start(stepIndex);
                this.isInitialized = true;
            }
        });
    }

    public async start(stepIndex = 0): Promise<void> {
        const currentRawStep = this.getRawStepByIndex(stepIndex);
        if (currentRawStep) {
            await this.runShowEvents(currentRawStep);
            this.driverInstance.drive(stepIndex);
        }
    }

    public refresh(): void {
        this.driverInstance.refresh();
    }

    public destroy(): void {
        this.driverInstance.destroy();
    }

    public setSteps(steps: TourStep[]): void {
        const processedSteps = steps.map((step) => {
            const stepData = { ...step };
            if (typeof stepData.element === "string") {
                const elSelector = stepData.element as string;
                stepData.element = () => {
                    return (
                        stepData.frame
                            ? this.getElementInsideFrame(
                                  stepData.frame,
                                  elSelector
                              )
                            : this.getElement(elSelector)
                    ) as Element;
                };
            }
            if (stepData.frame) {
                const el = this.getElement(stepData.frame);
                if (el) {
                    stepData.popover = {
                        ...stepData.popover,
                        offsetX: el.getBoundingClientRect().left,
                        offsetY: el.getBoundingClientRect().top,
                    };
                }
            }
            return stepData;
        });
        this.driverInstance.setSteps(processedSteps);
    }

    public hasNextStep(): boolean {
        return this.driverInstance.hasNextStep();
    }

    public moveNext(): void {
        this.driverInstance.moveNext();
    }

    public movePrevious(): void {
        this.driverInstance.movePrevious();
    }

    public isActive(): boolean {
        return this.driverInstance.isActive();
    }

    public getCurrentStepIndex(): number {
        return this.driverInstance.getActiveIndex() || 0;
    }

    public getElement(selector: string) {
        const element = document.querySelector(selector);
        if (!element) {
            console.error(`No element found for selector: "${selector}"`);
        }
        return element;
    }

    public getElements(selector: string) {
        const elements = document.querySelectorAll(selector);
        if (!elements.length) {
            console.error(`No elements found for selector: "${selector}"`);
        }
        return elements;
    }

    public getElementInsideFrame(
        frameSelector: string,
        elementSelector: string
    ): HTMLElement {
        let iframe: HTMLIFrameElement | null;

        if (frameSelector.startsWith("#")) {
            const id = frameSelector.slice(1);
            iframe = document.getElementById(id) as HTMLIFrameElement | null;
        } else if (frameSelector.startsWith(".")) {
            const className = frameSelector.slice(1);
            iframe = document.querySelector(
                `iframe.${className}`
            ) as HTMLIFrameElement | null;
        } else {
            iframe = document.querySelector(
                `iframe[name="${frameSelector}"]`
            ) as HTMLIFrameElement | null;
        }

        if (!iframe) {
            throw new Error(`No iframe found for selector: ${frameSelector}`);
        }
        const doc = iframe.contentDocument;
        if (!doc) {
            throw new Error(
                `Iframe not loaded or cross-origin: ${frameSelector}`
            );
        }

        const element = doc.querySelector<HTMLElement>(elementSelector);
        if (!element) {
            throw new Error(
                `No element found for selector: "${elementSelector}" in iframe: "${frameSelector}"`
            );
        }

        return element;
    }

    public getElementsInsideFrame(
        frameSelector: string,
        elementSelector: string
    ): NodeListOf<HTMLElement> {
        let iframe = this.getElement(frameSelector) as HTMLIFrameElement;

        if (!iframe) {
            throw new Error(`No iframe found for selector: ${frameSelector}`);
        }
        const doc = iframe.contentDocument;
        if (!doc) {
            throw new Error(
                `Iframe not loaded or cross-origin: ${frameSelector}`
            );
        }

        const elements = doc.querySelectorAll<HTMLElement>(elementSelector);
        if (!elements.length) {
            throw new Error(
                `No elements found for selector: "${elementSelector}" in iframe: "${frameSelector}"`
            );
        }

        return elements;
    }

    public getRawStepByIndex(index: number): TourStep | undefined {
        return this.rawSteps[index];
    }

    public async cleanup(runCallback = true): Promise<void> {
        this.cleanupEventListeners();
        this.destroy();
        if (this.resizeObserver) {
            this.resizeObserver.disconnect();
            this.resizeObserver = null;
        }
        if (runCallback) {
            await this.config.onTourCleanup?.(this.identifier);
        }
    }

    public eventsListener(event: Event): void {
        const rawStep = this.getRawStepByIndex(this.getCurrentStepIndex());
        const action = Object.values(rawStep?.userActions || {}).find(
            (action) => action.event === event.type
        );
        // Action matched
        if (action) {
            const element = rawStep?.frame
                ? this.getElementInsideFrame(rawStep.frame, action.target)
                : this.getElement(action.target);
            if (!element) {
                return;
            }
            if ((event.target as HTMLElement)?.closest?.(action.target)) {
                action.isDone = true;
                if (
                    Object.values(rawStep?.userActions || {}).every(
                        (action) => action.isDone
                    )
                ) {
                    this.cleanupEventListeners();
                    this.onNextClick();
                }
            }
        }
    }

    public cleanupEventListeners(): void {
        const rawStep = this.getRawStepByIndex(this.getCurrentStepIndex());

        if (rawStep && rawStep.requireUserActions && rawStep.userActions) {
            rawStep.userActions.forEach((action) => {
                const elements = rawStep?.frame
                    ? this.getElementsInsideFrame(rawStep.frame, action.target)
                    : this.getElements(action.target);
                if (!elements.length) {
                    return;
                }
                ACTION_TYPES.forEach((eventName) => {
                    elements.forEach((element) => {
                        element.removeEventListener(
                            eventName,
                            this.eventsListener
                        );
                    });
                });
            });
        }
    }

    public async onPopoverRender(
        popover: PopoverDOM,
        opts: { config: Config; state: State; driver: Driver }
    ): Promise<void> {
        const currentStepIndex = this.getCurrentStepIndex();
        const rawStep = this.getRawStepByIndex(currentStepIndex);
        await this.config?.onStepChange?.(this.identifier, currentStepIndex);
        if (this.hasNextStep()) {
            const skipButton = document.createElement("button");
            const skipButtonLabel =
                rawStep?.skipButtonText ||
                this.config.driverConfig?.skipBtnText ||
                "Skip";
            skipButton.innerText = skipButtonLabel;
            popover.footerButtons.prepend(skipButton);

            skipButton.addEventListener("click", () => {
                this.onSkipClick();
            });
        }

        if (rawStep && rawStep.requireUserActions && rawStep.userActions) {
            rawStep.userActions.forEach((action) => {
                const elements = rawStep?.frame
                    ? this.getElementsInsideFrame(rawStep.frame, action.target)
                    : this.getElements(action.target);
                if (!elements.length) {
                    return;
                }
                action.isDone = false;
                ACTION_TYPES.forEach((eventName) => {
                    elements.forEach((element) => {
                        element.addEventListener(
                            eventName,
                            this.eventsListener
                        );
                    });
                });
            });
        }
    }

    public async disableForAsyncAction(
        action: () => Promise<any>
    ): Promise<void> {
        const { popover } = this.driverInstance.getState();
        if (popover) {
            popover.nextButton.classList.add("driver-popover-btn-disabled");
            popover.previousButton.classList.add("driver-popover-btn-disabled");
            popover.closeButton.classList.add("driver-popover-btn-disabled");
        }
        await action();
        if (popover) {
            popover.nextButton.classList.remove("driver-popover-btn-disabled");
            popover.previousButton.classList.remove(
                "driver-popover-btn-disabled"
            );
            popover.closeButton.classList.remove("driver-popover-btn-disabled");
        }
    }

    public async triggerTourEvent(event: TourEvent): Promise<void> {
        if (event.delayBefore && event.delayBefore > 0) {
            await sleep(event.delayBefore);
        }

        const rawStep = this.getRawStepByIndex(this.getCurrentStepIndex());
        const element =
            event.frame || rawStep?.frame
                ? this.getElementInsideFrame(
                      event?.frame || rawStep?.frame || "",
                      event.target
                  )
                : this.getElement(event.target);
        if (!element) {
            return;
        }

        element.dispatchEvent(new Event(event.event));

        if (event.delayAfter && event.delayAfter > 0) {
            await sleep(event.delayAfter);
        }
    }

    public async runShowEvents(rawStep: TourStep): Promise<void> {
        if (rawStep.onShow) {
            for (const event of rawStep.onShow) {
                await this.disableForAsyncAction(
                    async () => await this.triggerTourEvent(event)
                );
            }
        }
    }

    public async runExitEvents(rawStep: TourStep): Promise<void> {
        if (rawStep.onExit) {
            for (const event of rawStep.onExit) {
                await this.disableForAsyncAction(
                    async () => await this.triggerTourEvent(event)
                );
            }
        }
    }

    public async onSkipClick(): Promise<void> {
        const currentStepIndex = this.getCurrentStepIndex();
        const rawCurrentStep = this.getRawStepByIndex(currentStepIndex);
        if (rawCurrentStep) {
            await this.runExitEvents(rawCurrentStep);
        }
        await this.config?.onClose?.(this.identifier);
        this.cleanup(false);
    }

    public async onPrevClick(): Promise<void> {
        const currentStepIndex = this.getCurrentStepIndex();
        const rawCurrentStep = this.getRawStepByIndex(currentStepIndex);
        if (rawCurrentStep) {
            await this.runExitEvents(rawCurrentStep);
        }
        this.cleanupEventListeners();
        const prevStep = currentStepIndex - 1;
        const rawPrevStep = this.getRawStepByIndex(prevStep);
        if (rawPrevStep) {
            if (rawPrevStep.sleep) {
                await this.disableForAsyncAction(
                    async () => await sleep(rawPrevStep.sleep)
                );
            }
            await this.runShowEvents(rawPrevStep);
        }
        this.movePrevious();
    }

    public async onNextClick(): Promise<void> {
        const currentStepIndex = this.getCurrentStepIndex();
        const rawCurrentStep = this.getRawStepByIndex(currentStepIndex);
        if (rawCurrentStep) {
            await this.runExitEvents(rawCurrentStep);
        }
        this.cleanupEventListeners();
        if (!this.hasNextStep()) {
            this.cleanup();
        } else {
            const nextStep = this.getCurrentStepIndex() + 1;
            const rawNextStep = this.getRawStepByIndex(nextStep);
            if (rawNextStep) {
                if (rawNextStep.sleep) {
                    await this.disableForAsyncAction(
                        async () => await sleep(rawNextStep.sleep)
                    );
                }
                await this.runShowEvents(rawNextStep);
            }
            this.moveNext();
        }
    }
}
