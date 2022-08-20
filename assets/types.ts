export interface HttpError {
    parent: 'HttpError';
    status: number;
    detail: string;
    class: string;
}

class AbstractHttpError implements HttpError {
    parent: 'HttpError';
    status: number;
    detail: string;
    class: string;

    constructor (
        status: number,
        detail: string,
        className: string,
    ) {
        this.parent = 'HttpError';
        this.status = status;
        this.detail = detail;
        this.class = className;
    }
}

export class BadRequestError extends AbstractHttpError {}
export class NotFoundError extends AbstractHttpError {}

export class GeneralError extends Error {
    constructor (
        name: string,
        message: string,
    ) {
        super(message);
        this.name = name;
    }
}
