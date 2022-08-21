/**
 * Inspired by JS Simple Loader
 * https://github.com/Matheus2212/js-simple-loader
 */

export default class Loader {
    private static loader: HTMLDivElement | null = null;

    private static readonly body: HTMLElement = document.body;
    private static readonly html: string = '<span><svg width="40" height="40" version="1.1" xmlns="http://www.w3.org/2000/svg"><circle cx="20" cy="20" r="15"></svg></span>';
    private static readonly cssClass: string = 'loader';

    public static open = () => {
        if (!this.isOpen()) {
            this.loader = document.createElement('div');
            this.loader.classList.add('loader_website');
            this.loader.innerHTML = this.html;

            this.body.append(this.loader);
            this.body.classList.add(this.cssClass);
        }
    };

    public static close = () => {
        if (!this.isOpen() || this.loader === null) {
            throw new Error('Loader is not opened');
        }

        this.body.classList.remove(this.cssClass);
        this.loader.remove();
    };

    private static isOpen = () => {
        return this.body.classList.contains(this.cssClass);
    };
}
