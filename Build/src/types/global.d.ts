import Guide from "../guide";

declare global {
    interface Window {
        _typo3GuideInstance?: Guide;
    }
}
