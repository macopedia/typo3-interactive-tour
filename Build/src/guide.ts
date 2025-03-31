import AjaxRequest from "@macopedia/typo3-interactive-tour/typo3/ajax-request";
import Tour from "@macopedia/typo3-interactive-tour/tour";
import type { Side, Alignment } from "./driver/popover";
import type { TourEvent } from "./tour";
import type { AllowedButtons } from "./driver/popover";
import "./guide.css";

interface TourStep {
    title: string;
    content: string;
    target?: string;
    side?: Side;
    align?: Alignment;
    enableInteraction?: boolean;
    nextButtonText?: string;
    previousButtonText?: string;
    skipButtonText?: string;
    frame?: string;
    requireUserActions?: boolean;
    sleep?: number;
    events?: {
        onShow?: TourEvent[];
        onExit?: TourEvent[];
        userActions?: TourEvent[];
    };
}

interface SingleTourResponse {
    identifier: string;
    title: string;
    description: string;
    steps: TourStep[];
    moduleName: string;
    moduleRoute: string;
    currentStep: number;
    isCompleted: boolean;
    isDisabled: boolean;
    finishButtonText: string | null;
    nextButtonText: string | null;
    previousButtonText: string | null;
    skipButtonText: string | null;
    nextTourIdentifier?: string;
    isStandalone?: boolean;
}

export default class Guide {
    private tour: Tour | null = null;
    private toursData: SingleTourResponse[] = [];

    constructor() {
        this.setup();
    }

    public async fetchAllTours(): Promise<void> {
        try {
            const request = new AjaxRequest(
                TYPO3.settings.ajaxUrls.guide_tour_get_all
            );
            const response = await request.get();
            this.toursData = (
                (await response.resolve()) as SingleTourResponse[]
            )?.sort((a, b) => Number(b.isStandalone) - Number(a.isStandalone));
        } catch (error) {
            console.error(error);
        }
    }

    public async completeTour(identifier: string): Promise<void> {
        try {
            const request = new AjaxRequest(
                TYPO3.settings.ajaxUrls.guide_tour_complete
            );
            const body = { tourIdentifier: identifier };
            await request.post(body);
        } catch (error) {
            console.error(error);
        }
    }

    public async restartTour(identifier: string): Promise<void> {
        try {
            const request = new AjaxRequest(
                TYPO3.settings.ajaxUrls.guide_tour_restart
            );
            const body = { tourIdentifier: identifier };
            await request.post(body);
        } catch (error) {
            console.error(error);
        }
    }

    public async disableTour(identifier: string): Promise<void> {
        try {
            const request = new AjaxRequest(
                TYPO3.settings.ajaxUrls.guide_tour_disable
            );
            const body = { tourIdentifier: identifier };
            await request.post(body);
        } catch (error) {
            console.error(error);
        }
    }

    public async setCurrentStep(
        identifier: string,
        stepIndex: number
    ): Promise<void> {
        try {
            const request = new AjaxRequest(
                TYPO3.settings.ajaxUrls.guide_tour_set_current_step
            );
            const body = { tourIdentifier: identifier, step: stepIndex };
            await request.post(body);
        } catch (error) {
            console.error(error);
        }
    }

    public async onStepChange(
        identifier: string,
        stepIndex: number
    ): Promise<void> {
        const currentTour = this.toursData.find(
            (tour) => tour.identifier === identifier
        );
        if (currentTour) {
            await this.setCurrentStep(identifier, stepIndex + 1);
            currentTour.currentStep = stepIndex + 1;
        }
    }

    public async onTourCleanup(identifier?: string): Promise<void> {
        if (identifier) {
            const currentTour = this.toursData.find(
                (tour) => tour.identifier === identifier
            );

            await this.fetchAllTours();

            if (window.location.pathname === "/typo3/module/help/guide") {
                window.top?.location?.reload();
            }

            const nextIdentifierTour = this.toursData.find(
                (tour) => tour.identifier === currentTour?.nextTourIdentifier
            );
            if (
                currentTour?.nextTourIdentifier &&
                nextIdentifierTour?.isCompleted === false &&
                nextIdentifierTour?.isDisabled === false
            ) {
                this.runTour(currentTour.nextTourIdentifier);
            } else {
                this.runFirstAllowedTour();
            }
        }
    }

    public async onClose(identifier?: string): Promise<void> {
        const currentTour = this.toursData.find(
            (tour) => tour.identifier === identifier
        );
        if (
            identifier &&
            currentTour &&
            (this.tour?.hasNextStep() || currentTour?.steps.length === 1)
        ) {
            await this.disableTour(identifier);
            await this.fetchAllTours();
        }
    }

    public createTour(data: SingleTourResponse): void {
        const steps =
            Object.values(data?.steps || [])?.map((step, index) => {
                return {
                    element: step.target,
                    disableActiveInteraction: !step.enableInteraction,
                    requireUserActions: step.requireUserActions,
                    onShow: step.events?.onShow,
                    onExit: step.events?.onExit,
                    userActions: step.events?.userActions,
                    frame: step.frame,
                    sleep: step.sleep,
                    popover: {
                        title: step.title,
                        description: step.content,
                        side: step.side,
                        align: step.align,
                        showButtons:
                            index === 0
                                ? (["next"] as AllowedButtons[])
                                : undefined,
                        prevBtnText: step.previousButtonText,
                        nextBtnText: step.nextButtonText,
                        skipBtnText: step.skipButtonText,
                        disableButtons:
                            step.requireUserActions &&
                            step.events?.userActions?.length
                                ? (["next"] as AllowedButtons[])
                                : undefined,
                    },
                };
            }) || [];
        if (steps.length) {
            this.tour = new Tour({
                identifier: data.identifier,
                steps,
                stepIndex: Math.max(0, data.currentStep - 1),
                onTourCleanup: this.onTourCleanup.bind(this),
                onStepChange: this.onStepChange.bind(this),
                onClose: this.onClose.bind(this),
                driverConfig: {
                    showProgress: steps.length > 1,
                    prevBtnText:
                        data.previousButtonText ||
                        TYPO3.lang["guide.buttonPrevious"],
                    nextBtnText:
                        data.nextButtonText || TYPO3.lang["guide.buttonNext"],
                    doneBtnText:
                        data.finishButtonText ||
                        TYPO3.lang["guide.buttonFinish"],
                    skipBtnText:
                        data.skipButtonText || TYPO3.lang["guide.buttonSkip"],
                },
            });
        }
    }

    public runFirstAllowedTour(): void {
        if (this.tour?.isActive()) {
            return;
        }
        // Find first not completed or not disabled tour for current module
        const targetTour = this.toursData.find((tour) => {
            return (
                !tour.isCompleted &&
                !tour.isDisabled &&
                (tour.isStandalone ||
                    tour.moduleRoute?.split?.("?")?.[0] ===
                        window.location.pathname)
            );
        });
        if (targetTour) {
            this.createTour(targetTour);
        }
    }

    public async runTour(identifier: string): Promise<void> {
        const tour = this.toursData.find(
            (tour) => tour.identifier === identifier
        );

        if (tour?.isCompleted) {
            await this.restartTour(identifier);
            await this.fetchAllTours();
        }

        const updatedTour = this.toursData.find(
            (tour) => tour.identifier === identifier
        );

        if (updatedTour) {
            updatedTour.isCompleted = false;
            updatedTour.isDisabled = false;
            if (updatedTour.moduleRoute) {
                window.TYPO3?.ModuleMenu?.App?.showModule?.(
                    updatedTour.moduleName
                );
            } else {
                this.createTour(updatedTour);
            }
        }
    }

    public changeViewHandler(): void {
        this.runFirstAllowedTour();
    }

    public async setup(): Promise<void> {
        await this.fetchAllTours();
        if (this.toursData.length) {
            document.addEventListener(
                "typo3-module-loaded",
                this.changeViewHandler.bind(this)
            );
        }
    }

    public destroy(): void {
        this.tour?.cleanup();
        this.tour = null;
        document.removeEventListener(
            "typo3-module-loaded",
            this.changeViewHandler.bind(this)
        );
    }
}

// Initialize guide instance as singleton
if (!window.top?._typo3GuideInstance) {
    window._typo3GuideInstance = new Guide();
}

document
    .querySelectorAll("button[data-tour-trigger][data-tour-identifier]")
    .forEach((button) => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            const identifier = (
                event.currentTarget as HTMLButtonElement
            ).getAttribute("data-tour-identifier");
            if (identifier) {
                window.top?._typo3GuideInstance?.runTour(identifier);
            }
        });
    });

document
    .querySelectorAll("button[data-tour-complete][data-tour-identifier]")
    .forEach((button) => {
        button.addEventListener("click", async (event) => {
            event.preventDefault();
            const identifier = (
                event.currentTarget as HTMLButtonElement
            ).getAttribute("data-tour-identifier");
            if (identifier) {
                await window.top?._typo3GuideInstance?.completeTour(identifier);
                window.top?.location.reload();
            }
        });
    });
