export interface ErrorResponse {
    error: HttpError;
}

export interface HttpError {
    parent: 'HttpError';
    type: string;
    message: string;
}

class AbstractHttpError implements HttpError {
    parent: 'HttpError';
    type: string;
    message: string;

    constructor (
        type: string,
        message: string,
    ) {
        this.parent = 'HttpError';
        this.type = type;
        this.message = message;
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
