import axios, { AxiosError, AxiosResponse } from 'axios';
import {
    BadRequestError,
    ErrorResponse,
    HttpError,
    NotFoundError,
} from '../types';

export default class Request {
    public static get = async <T>(
        url: string,
        expectedStatusCodes: number[],
    ): Promise<AxiosResponse<T> | HttpError> => {
        const promiseResponse = axios.get<T>(url, {
            timeout: 20000,
        });

        const response = await this.handleResponse(promiseResponse, expectedStatusCodes);

        if (axios.isAxiosError(response)) {
            const errorResponse = response.response;

            if (errorResponse === undefined) {
                throw response;
            }

            const error = errorResponse.data.error;

            switch (errorResponse.status) {
                case 400:
                    return new BadRequestError(
                        error.type,
                        error.message,
                    );
                case 404:
                    return new NotFoundError(
                        error.type,
                        error.message,
                    );
            }

            throw new Error(`Status code "${errorResponse.status}" is not implemented in Request.get method`);
        }

        return response;
    };

    private static handleResponse = async <T>(
        promiseResponse: Promise<AxiosResponse<T>>,
        expectedStatusCodes: number[],
    ): Promise<AxiosResponse<T> | AxiosError<ErrorResponse>> => {
        let response: AxiosResponse<T> | AxiosError<ErrorResponse>;
        let statusCode: number;

        try {
            response = await promiseResponse;
            statusCode = response.status;
        } catch (error) {
            const e = error as AxiosError<ErrorResponse>;

            // empty response - probably request timeout
            if (e.response === undefined) {
                throw e;
            }

            response = e;
            statusCode = e.response.status;
        }

        if (!expectedStatusCodes.includes(statusCode)) {
            throw new Error(`Expected status code "${expectedStatusCodes.join(',')}", was "${statusCode}"`);
        }

        return response;
    };

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    public static isInstanceOfHttpError = (error: Record<string, any>): error is HttpError => {
        return error.parent === 'HttpError';
    };
}
