import AjaxRequest from "@macopedia/typo3-interactive-tour/typo3/ajax-request";
document
    .querySelector("#switch-off-tours")
    ?.addEventListener("change", async (event) => {
        const target = event.target as HTMLInputElement;
        console.log(target);
        if (target && !target.disabled) {
            target.disabled = true;
            try {
                const request = new AjaxRequest(
                    TYPO3.settings.ajaxUrls[
                        target.checked
                            ? "guide_tours_enable"
                            : "guide_tours_disable"
                    ]
                );
                await request.post({});
                window.top?.window.location.reload();
            } catch (error) {
                console.error(error);
            } finally {
                target.disabled = false;
            }
        }
    });
