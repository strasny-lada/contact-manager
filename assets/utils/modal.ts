import { Modal } from 'bootstrap';

export const showModal = (
    title: string,
    content: string,
) => {
    const modalElement = document.getElementById('modal');
    if (modalElement === null) {
        throw new Error('Modal element not found');
    }

    // fill the title
    const modalTitleElement = modalElement.querySelector<HTMLElement>('.modal-title');
    if (modalTitleElement === null) {
        throw new Error('Title element with class "modal-title" not found');
    }
    modalTitleElement.innerText = title;

    // fill the content
    const modalBodyElement = modalElement.querySelector<HTMLElement>('.modal-body');
    if (modalBodyElement === null) {
        throw new Error('Body element with class "modal-body" not found');
    }
    modalBodyElement.innerText = content;

    // clear the modal after close
    modalElement.addEventListener('hide.bs.modal', (event: Event) => {
        const target = event.target as HTMLElement | null;
        if (target === null) {
            throw new Error('Target element not found');
        }

        const targetTitleElement = target.querySelector<HTMLElement>('.modal-title');
        if (targetTitleElement === null) {
            throw new Error('Target title element with class "modal-title" not found');
        }
        targetTitleElement.innerText = '';

        const targetBodyElement = target.querySelector<HTMLElement>('.modal-body');
        if (targetBodyElement === null) {
            throw new Error('Target body element with class "modal-body" not found');
        }
        targetBodyElement.innerText = '';
    });

    new Modal(modalElement).show();
};
