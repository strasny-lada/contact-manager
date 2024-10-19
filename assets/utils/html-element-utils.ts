export const getHtmlElementFullHeight = (el: HTMLElement): number => {
    // Get the computed style of the element to access margin values
    const computedStyle = window.getComputedStyle(el);

    // Get the margin values and parse them as integers (they come as strings with units)
    const marginTop = parseInt(computedStyle.marginTop, 10);
    const marginBottom = parseInt(computedStyle.marginBottom, 10);

    // Get the offsetHeight which includes padding, border, and content
    const fullHeight = el.offsetHeight;

    // Add the margins to the full height
    return fullHeight + marginTop + marginBottom;
};
