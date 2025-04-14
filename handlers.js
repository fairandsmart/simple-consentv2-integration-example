let iFrameResizeAlreadyInitialized;

function initIframeResizer(frameName) {

    if (!iFrameResizeAlreadyInitialized) {
        iFrameResizeAlreadyInitialized = true;
        iFrameResize({
            log: true,
            heightCalculationMethod: 'max',
            onClose: function () {
                return false
            },
            onMessage: function () {
                return
            },
        }, frameName);
    }
}
