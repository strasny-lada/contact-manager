import React from 'react';
import { htmlElementFadeOut } from "../../utils/html-element-fade-out";

interface FlashMessageProps {
    type: string;
    message: string;
    closeHandlerCallback: Function;
}

const handleClose = (e: any, closeHandlerCallback: Function) => {
    const flashMessage = e.target.closest('.alert');

    htmlElementFadeOut(flashMessage, 500).then(r => {
        flashMessage.remove();
    });

    closeHandlerCallback();
}

const FlashMessage = (props: FlashMessageProps) => {
    return (
        <div className={`alert alert-${props.type}`}>
            {props.message}
            <button
                type="button"
                className="btn-close"
                onClick={(e) => handleClose(e, props.closeHandlerCallback)}
            ></button>
        </div>
    );
};

export default FlashMessage;