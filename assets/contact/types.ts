export interface PageResponse {
    page: Page;
}

export interface Page {
    url: string;
    title: string;
    content: string;
}

export interface Notice {
    name: string;
    notice: string;
}
