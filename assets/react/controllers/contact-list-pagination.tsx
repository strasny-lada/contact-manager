import {PaginationData, Texts} from "../../contact/types";

interface ContactListPaginationProps {
    paginationData: PaginationData,
    texts: Texts,
    loadPageHandler: Function,
}

const ContactListPagination = (props: ContactListPaginationProps) => {
    return (
        <div className="navigation">
            <nav>
                <ul className="pagination">
                    {props.paginationData.previous !== undefined && <li className="page-item">
                        <button type="button" className="page-link" rel="prev" onClick={() => props.loadPageHandler(props.paginationData.previous)}>&laquo;&nbsp;{props.texts['app.pagination.previous']}</button>
                    </li>}

                    {props.paginationData.previous === undefined && <li className="page-item disabled">
                        <span className="page-link">&laquo;&nbsp;{props.texts['app.pagination.previous']}</span>
                    </li>}

                    {props.paginationData.startPage > 1 && (() => {
                        const components = [];

                        components.push(<li className="page-item">
                            <button type="button" className="page-link" onClick={() => props.loadPageHandler(1)}>1</button>
                        </li>);

                        if (props.paginationData.startPage === 3) {
                            components.push(<li className="page-item">
                                <button type="button" className="page-link" onClick={() => props.loadPageHandler(2)}>2</button>
                            </li>);
                        } else if (props.paginationData.startPage !== 2) {
                            components.push(<li className="page-item disabled">
                                <span className="page-link">&hellip;</span>
                            </li>);
                        }

                        return <>{components}</>;
                    })()}

                    {props.paginationData.pagesInRange.map((pageNumber) => {
                        if (pageNumber !== props.paginationData.current) {
                            return <li className="page-item">
                                <button type="button" className="page-link" onClick={() => props.loadPageHandler(pageNumber)}>{pageNumber}</button>
                            </li>;
                        } else {
                            return <li className="page-item active">
                                <span className="page-link">{pageNumber}</span>
                            </li>;
                        }
                    })}

                    {props.paginationData.pageCount > props.paginationData.endPage && (() => {
                        const components = [];

                        if (props.paginationData.pageCount > (props.paginationData.endPage + 1)) {
                            if (props.paginationData.pageCount > (props.paginationData.endPage + 2)) {
                                components.push(<li className="page-item disabled">
                                    <span className="page-link">&hellip;</span>
                                </li>);
                            } else {
                                components.push(<li className="page-item">
                                    <button type="button" className="page-link" onClick={() => props.loadPageHandler(props.paginationData.pageCount - 1)}>{props.paginationData.pageCount - 1}</button>
                                </li>);
                            }
                        }
                        components.push(<li className="page-item">
                            <button type="button" className="page-link" onClick={() => props.loadPageHandler(props.paginationData.pageCount)}>{props.paginationData.pageCount}</button>
                        </li>);

                        return <>{components}</>;
                    })()}

                    {props.paginationData.next !== undefined && <li className="page-item">
                        <button type="button" className="page-link" rel="next" onClick={() => props.loadPageHandler(props.paginationData.next)}>{props.texts['app.pagination.next']}&nbsp;&raquo;</button>
                    </li>}

                    {props.paginationData.next === undefined && <li className="page-item disabled">
                        <span className="page-link">{props.texts['app.pagination.next']}&nbsp;&raquo;</span>
                    </li>}
                </ul>
            </nav>
        </div>
    );
}

export default ContactListPagination;
