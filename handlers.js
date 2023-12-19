let iFrameResizeAlreadyInitialized;

function initIframeResizer(frameName) {

    if (!iFrameResizeAlreadyInitialized) {
        iFrameResizeAlreadyInitialized = true;
        iFrameResize({
            log: false,
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
