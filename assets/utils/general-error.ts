import { GeneralError } from '../types';
import { showModal } from './modal';

let generalError: GeneralError | undefined;

const buildGeneralError = () => {
    if (generalError instanceof GeneralError) {
        return;
    }

    const generalErrorAsJSON = document.body.getAttribute('data-general-error');
    if (generalErrorAsJSON === null) {
        throw new Error('General error data not defined');
    }

    try {
        generalError = <GeneralError>JSON.parse(generalErrorAsJSON);
    } catch (error) {
        if (error instanceof SyntaxError) {
            throw new Error('General error data object is not valid JSON');
        } else {
            throw error;
        }
    }
};

export const showGeneralError = () => {
    buildGeneralError();

    if (generalError === undefined) {
        throw new Error('GeneralError is not build');
    }

    showModal(
        generalError.name,
        generalError.message,
        {},
    );
};
