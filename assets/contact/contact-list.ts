import Loader from '../utils/loader/loader';
import Request from '../utils/request';
import { AxiosResponse } from 'axios';
import { CONTACT_LIST_PAGE_LOAD } from '../routes';
import { HttpError } from '../types';
import { Notice, PageResponse } from './types';
import { showGeneralError } from '../utils/general-error';
import { showModal } from '../utils/modal';

document.addEventListener('DOMContentLoaded', () => {
    initContactList();
});

const initContactList = () => {
    const pageLinks = document.querySelectorAll<HTMLAnchorElement>('.pagination .page-link');
    pageLinks.forEach((pageLink: HTMLAnchorElement) => {
        pageLink.addEventListener('click', (event: Event) => {
            event.preventDefault();

            const pageLink = <HTMLAnchorElement> event.target;
            const pageUrl = pageLink.getAttribute('href');
            if (pageUrl === null) {
                showGeneralError();
                throw new Error('Page link should not be empty');
            }

            const pageNumber = Number(pageUrl.substring(pageUrl.lastIndexOf('/') + 1));
            if (isNaN(pageNumber)) {
                showGeneralError();
                throw new Error('Page number is not numeric');
            }

            Loader.open();

            void loadPage(pageNumber);
        });
    });

    const noticeButtons = document.querySelectorAll<HTMLAnchorElement>('.list-wrapper .notice-button');
    noticeButtons.forEach((noticeButton: HTMLAnchorElement) => {
        noticeButton.addEventListener('click', (event: Event) => {
            event.preventDefault();

            const noticeButton = <HTMLAnchorElement> event.target;

            const noticeDataSerialized = String(noticeButton.getAttribute('data-notice-object'));
            if (noticeDataSerialized === null) {
                showGeneralError();
                throw new Error('Notice data object is empty');
            }

            let noticeData: Notice | undefined;
            try {
                noticeData = <Notice>JSON.parse(noticeDataSerialized);
            } catch (error) {
                showGeneralError();
                if (error instanceof SyntaxError) {
                    throw new Error('Notice data object is not valid JSON');
                } else {
                    throw error;
                }
            }

            showModal(
                noticeData.name,
                noticeData.notice,
            );
        });
    });
};

const loadPage = async (pageNumber: number) => {
    const contactListWrapper = document.querySelector<HTMLDivElement>('.list-wrapper');
    if (contactListWrapper === null) {
        Loader.close();
        showGeneralError();
        throw new Error('Contact list wrapper not found');
    }

    const url = CONTACT_LIST_PAGE_LOAD.replace('{page}', String(pageNumber));

    let response: AxiosResponse<PageResponse> | HttpError;
    try {
        response = await Request.get(url, [200,400,404]);
    } catch (error) {
        Loader.close();
        showGeneralError();
        throw error;
    }

    if (Request.isInstanceOfHttpError(response)) {
        Loader.close();
        showGeneralError();
        throw new Error(`[${response.status}] ${response.detail}`);
    }

    const page = response.data.page;

    contactListWrapper.innerHTML = page.content;

    window.history.pushState({}, '', page.url);
    document.title = page.title;

    initContactList();

    Loader.close();
};
