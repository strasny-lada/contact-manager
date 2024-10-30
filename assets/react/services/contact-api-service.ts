import Request from '../../utils/request';
import { AxiosResponse } from 'axios';
import { CONTACT_LIST_PAGE } from '../../contact/routes';
import { HttpError } from '../../types';
import { PageResponse } from '../../contact/types';

export class ContactApiService {

    fetchContactListPage (pageNumber: number): Promise<AxiosResponse<PageResponse> | HttpError> {
        const url = CONTACT_LIST_PAGE.replace('{page}', String(pageNumber));

        return Request.get(url, [200, 400, 404]);
    }

}