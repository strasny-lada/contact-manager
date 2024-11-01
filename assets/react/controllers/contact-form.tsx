import React, { useState } from 'react';
import { Contact, ContactFormFields, Texts } from "../../contact/types";
import { ContactApiService } from "../services/contact-api-service";
import Request from "../../utils/request";
import FlashMessage from "./flash-message";

interface ContactFormProps {
    contact: Contact|null;
    texts: Texts;
    csrfToken: string;
}

const ContactForm = (props: ContactFormProps) => {
    const isUpdate = props.contact !== null;

    const [contact, setContact] = useState<ContactFormFields>({
        firstname: props.contact !== null ? props.contact.firstname : '',
        lastname: props.contact !== null ? props.contact.lastname : '',
        email: props.contact !== null ? props.contact.email : '',
        phone: props.contact !== null ? props.contact.phone : '',
        notice: props.contact !== null ? props.contact.notice : '',
    });

    const [submittedContactName, setSubmittedContactName] = useState('');

    const [success, setSuccess] = useState(false);
    const [error, setError] = useState<Error|null>(null);
    const [violations, setViolations] = useState<ContactFormFields>({
        firstname: '',
        lastname: '',
        email: '',
        phone: '',
        notice: '',
    });

    const contactApiService = new ContactApiService();

    const handleInputChange = (e: any) => {
        const { name, value } = e.target;
        setContact({ ...contact, [name]: value });
    };

    const handleSubmit = (e: any) => {
        e.preventDefault();

        setSuccess(false);
        setError(null);

        if (!isUpdate) {
            contactApiService.createContact({ ...contact, _token: props.csrfToken })
                .then((response) => handleSubmitResponse(response))
                .catch(error => {
                    setError(new Error(error.message));
                });
        } else {
            contactApiService.updateContact({ ...contact, slug: props.contact?.slug, _token: props.csrfToken } as Contact)
                .then((response) => handleSubmitResponse(response))
                .catch(error => {
                    setError(new Error(error.message));
                });
        }
    };

    const handleSubmitResponse = (response: any) => {
        if (Request.isInstanceOfHttpError(response)) {
            if (response.class === 'App\\Exception\\Api\\ApiRequestValidationException') {
                setViolations(JSON.parse(response.detail));
                return;
            }

            setError(new Error(response.detail));
            throw new Error(`[${response.status}] ${response.detail}`);
        }

        if (!isUpdate) {
            setSubmittedContactName(response.data.contact.lastname.concat(' ', response.data.contact.firstname));
        } else {
            setSubmittedContactName(contact.lastname.concat(' ', contact.firstname));
        }

        setSuccess(true);

        // reset form
        if (!isUpdate) {
            setContact({
                firstname: '',
                lastname: '',
                email: '',
                phone: '',
                notice: '',
            });
        }
        setViolations({
            firstname: '',
            lastname: '',
            email: '',
            phone: '',
            notice: '',
        });
    };

    return (
        <>
            {success && !isUpdate && <FlashMessage
                type={'success'}
                message={props.texts['app.form.flash_message.added.success'].replace('%added_item%', submittedContactName)}
                closeHandlerCallback={() => setSuccess(false)}
            />}

            {success && isUpdate && <FlashMessage
                type={'success'}
                message={props.texts['app.form.flash_message.edited.success'].replace('%edited_item%', submittedContactName)}
                closeHandlerCallback={() => setSuccess(false)}
            />}

            {error !== null && <FlashMessage
                type={'danger'}
                message={error.message}
                closeHandlerCallback={() => setError(null)}
            />}

            <form name="contact_form" onSubmit={handleSubmit} noValidate>
                <div id="contact_form">
                    <div className="mb-3">
                        <label htmlFor="contact_form_firstname" className="form-label required">{props.texts['app.contact.firstname']}</label>
                        <input type="text" id="contact_form_firstname" name="firstname" className={`form-control ${violations.firstname ? 'is-invalid' : ''}`} value={contact.firstname} onChange={handleInputChange} required />
                        {violations.firstname && <div className="invalid-feedback d-block">{violations.firstname}</div>}
                    </div>
                    <div className="mb-3">
                        <label htmlFor="contact_form_lastname" className="form-label required">{props.texts['app.contact.lastname']}</label>
                        <input type="text" id="contact_form_lastname" name="lastname" className={`form-control ${violations.lastname ? 'is-invalid' : ''}`} value={contact.lastname} onChange={handleInputChange} required />
                        {violations.lastname && <div className="invalid-feedback d-block">{violations.lastname}</div>}
                    </div>
                    <div className="mb-3">
                        <label htmlFor="contact_form_email" className="form-label required">{props.texts['app.contact.email']}</label>
                        <input type="email" id="contact_form_email" name="email" className={`form-control ${violations.email ? 'is-invalid' : ''}`} value={contact.email} onChange={handleInputChange} required />
                        {violations.email && <div className="invalid-feedback d-block">{violations.email}</div>}
                    </div>
                    <div className="mb-3">
                        <label htmlFor="contact_form_phone" className="form-label">{props.texts['app.contact.phone']}</label>
                        <input type="text" id="contact_form_phone" name="phone" className={`form-control ${violations.phone ? 'is-invalid' : ''}`} value={contact.phone !== null ? contact.phone : ''} onChange={handleInputChange} />
                        {violations.phone && <div className="invalid-feedback d-block">{violations.phone}</div>}
                    </div>
                    <div className="mb-3">
                    <label htmlFor="contact_form_notice" className="form-label">{props.texts['app.contact.notice']}</label>
                        <textarea name="notice" id="contact_form_notice" className={`form-control ${violations.notice ? 'is-invalid' : ''}`} value={contact.notice !== null ? contact.notice : ''} onChange={handleInputChange}></textarea>
                        {violations.notice && <div className="invalid-feedback d-block">{violations.notice}</div>}
                    </div>
                    <div className="mb-3">
                        {!isUpdate && <button type="submit" name="submit" className="btn-primary btn">{props.texts['app.form.add']}</button>}
                        {isUpdate && <button type="submit" name="submit" className="btn-primary btn">{props.texts['app.form.edit']}</button>}
                    </div>
                </div>
            </form>
        </>
    );
};

export default ContactForm;