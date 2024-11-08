import Request from '../../utils/request';
import { AxiosResponse } from 'axios';
import {
    CONTACT_CREATE,
    CONTACT_DELETE,
    CONTACT_DELETE_FORM,
    CONTACT_LIST_PAGE,
    CONTACT_UPDATE,
} from '../../contact/routes';
import {
    ContactFormResponse,
    DeleteContactFormResponse,
    PageResponse,
} from '../../contact/types';
import { HttpError } from '../../types';

export class ContactApiService {

    fetchContactListPage (pageNumber: number): Promise<AxiosResponse<PageResponse> | HttpError> {
        const url = CONTACT_LIST_PAGE.replace('{page}', String(pageNumber));

        return Request.get(url, [200, 400, 404]);
    }

    createContact (contactData: object): Promise<AxiosResponse<ContactFormResponse> | HttpError> {
        return Request.post(CONTACT_CREATE, contactData, [201, 400, 404]);
    }

    updateContact (contactData: { slug: string }): Promise<AxiosResponse<null> | HttpError> {
        const url = CONTACT_UPDATE.replace('{slug}', contactData.slug);

        return Request.put(url, contactData, [204, 400, 404]);
    }

    fetchDeleteContactForm (): Promise<AxiosResponse<DeleteContactFormResponse> | HttpError> {
        return Request.get(CONTACT_DELETE_FORM, [200, 400, 404]);
    }

    deleteContact (slug: string, csrfToken: string): Promise<AxiosResponse<null> | HttpError> {
        const url = CONTACT_DELETE
            .replace('{slug}', slug)
            .replace('{csrfToken}', csrfToken);

        return Request.delete(url, [204, 400, 404]);
    }

}