import { ContactApiService } from '../services/contact-api-service';
import { Contact, ContactNoticeProps, PaginationData, Texts } from "../../contact/types";
import React, { useEffect, useState } from 'react';
import Request from "../../utils/request";
import ContactListPagination from './contact-list-pagination';
import { showModal } from "../../utils/modal";
import FlashMessage from "./flash-message";

interface ContactListProps {
    pageNumber: number;
}

const ContactList = (props: ContactListProps) => {
    const [renderList, setRenderList] = useState(false);

    const [pageUrl, setPageUrl] = useState('');
    const [pageTitle, setPageTitle] = useState('');
    const [pageItems, setPageItems] = useState<Array<Contact>>([]);
    const [texts, setTexts] = useState<Texts>({});
    const [urls, setUrls] = useState<Texts>({});
    const [paginationData, setPaginationData] = useState<PaginationData>({
        current: 0,
        endPage: 0,
        next: 0,
        pageCount: 0,
        pagesInRange: [],
        previous: 0,
        startPage: 0
    });

    const [error, setError] = useState<Error|null>(null);

    const contactApiService = new ContactApiService();

    useEffect(() => {
        updatePage(props.pageNumber);
    }, []); // The empty dependency array ensures this runs only once

    const updatePage = (pageNumber: number) => {
        contactApiService.fetchContactListPage(pageNumber)
            .then((response) => {
                setRenderList(true);

                if (Request.isInstanceOfHttpError(response)) {
                    setError(new Error(response.detail));
                    throw new Error(`[${response.status}] ${response.detail}`);
                }

                setPageUrl(response.data.page.url);
                setPageTitle(response.data.page.title);
                setPageItems(response.data.page.items);
                setPaginationData(response.data.page.paginationData);
                setTexts(response.data.texts);
                setUrls(response.data.urls);
            })
            .catch(error => {
                setRenderList(true);
                setError(new Error(error.message));
            });
    }

    const handleLoadPage = (pageNumber: number) => {
        updatePage(pageNumber);
    };

    const handleShowNotice = (data: ContactNoticeProps) => {
        if (data.notice === null) {
            throw new Error('Notice should not be null at this point.');
        }

        showModal(
            data.name,
            data.notice,
            {},
        );
    }

    window.history.pushState({}, '', pageUrl);
    document.title = pageTitle;

    return (
        <>
            {renderList && (() => {
                return <>
                    {error !== null && <FlashMessage type={'danger'} message={error.message} closeHandlerCallback={() => setError(null)}/>}

                    {error === null && (() => {
                        if (pageItems.length === 0) {
                            return <p className="lead">{texts['app.contact.list.empty']}</p>;
                        } else {
                            return <div className="list-wrapper">
                                <table className="table table-responsive">
                                    <thead>
                                    <tr>
                                        <th>{texts['app.contact.name']}</th>
                                        <th>{texts['app.contact.email']}</th>
                                        <th>{texts['app.contact.phone']}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {pageItems.map((pageItem: Contact) => (
                                        <tr className="contact-row test-contact-row" key={pageItem.slug}>
                                            <td className="test-contact-name">{pageItem.lastname} {pageItem.firstname}</td>
                                            <td className="test-contact-email">{pageItem.email}</td>
                                            <td className="test-contact-phone">{pageItem.phone}</td>
                                            <td>
                                                <button
                                                    className={`btn btn-primary notice-button test-contact-notice-button ${pageItem.notice === null ? 'disabled' : ''}`}
                                                    onClick={() => {
                                                        handleShowNotice({
                                                            name: pageItem.lastname + ' ' + pageItem.firstname,
                                                            notice: pageItem.notice,
                                                        })
                                                    }}
                                                >
                                                    {texts['app.contact.notice']}
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                    </tbody>
                                </table>

                                {paginationData.pageCount > 1 &&
                                    <ContactListPagination paginationData={paginationData}
                                                           texts={texts}
                                                           loadPageHandler={handleLoadPage}/>}
                            </div>;
                        }
                    })()}
                </>
            })()}
        </>
    );
};

export default ContactList;