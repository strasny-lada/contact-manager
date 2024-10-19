export const htmlElementFadeOut = async (
    el: HTMLElement,
    durationInMs: number,
    config: object = defaultFadeConfig(),
): Promise<void> => {
    return new Promise<void>((resolve) => {
        const animation = el.animate([
            {
                opacity: '1',
            },
            {
                opacity: '0', offset: 0.5,
            },
            {
                opacity: '0', offset: 1,
            },
        ], {
            duration: durationInMs, ...config,
        });
        animation.onfinish = () => resolve();
    });
};

const defaultFadeConfig = (): object => {
    return {
        easing: 'linear',
        iterations: 1,
        direction: 'normal',
        fill: 'forwards',
        delay: 0,
        endDelay: 0,
    };
};
