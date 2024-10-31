export interface PageResponse {
    page: Page;
    texts: Texts;
    urls: Texts;
}

export interface Page {
    number: number;
    url: string;
    title: string;
    items: Array<Contact>;
    paginationData: PaginationData
}

export interface Contact {
    firstname: string;
    lastname: string;
    email: string;
    phone: string|null;
    notice: string|null;
    slug: string;
}

export interface ContactFormFields {
    firstname: string;
    lastname: string;
    email: string;
    phone: string|null;
    notice: string|null;
}

export interface PaginationData {
    current: number;
    endPage: number;
    next: number|undefined;
    pageCount: number;
    pagesInRange: Array<number>;
    previous: number|undefined;
    startPage: number;
}

export interface Texts {
    [key: string]: string;
}

export interface ContactNoticeProps {
    name: string;
    notice: string|null;
}

export interface ContactFormResponse {
    contact: Contact,
}