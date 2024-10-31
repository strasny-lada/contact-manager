import Request from '../../utils/request';
import { AxiosResponse } from 'axios';
import { CONTACT_CREATE, CONTACT_LIST_PAGE } from '../../contact/routes';
import { ContactFormResponse, PageResponse } from '../../contact/types';
import { HttpError } from '../../types';

export class ContactApiService {

    fetchContactListPage (pageNumber: number): Promise<AxiosResponse<PageResponse> | HttpError> {
        const url = CONTACT_LIST_PAGE.replace('{page}', String(pageNumber));

        return Request.get(url, [200, 400, 404]);
    }

    createContact (contactData: object): Promise<AxiosResponse<ContactFormResponse> | HttpError> {
        return Request.post(CONTACT_CREATE, contactData, [201, 400, 404]);
    }

}