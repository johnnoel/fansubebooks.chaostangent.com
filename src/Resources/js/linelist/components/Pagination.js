import React, { Component, PropTypes } from 'react';

/**
 * Pagination component
 *
 * @author John Noel <john.noel@rckt.co.uk>
 * @package FansubEbooks
 */
class Pagination extends Component {
    renderPrevPage() {
        if (this.props.page > 1) {
            return <a href="" className="btn btn-outline pagination-prev">
                &larr; Previous page
            </a>;
        }

        return <div className="btn btn-outline btn-disabled pagination-prev" aria-disabled="true">
            &larr; Previous page
        </div>;
    }

    renderPage(number) {
        let classes = (number == this.props.page) ? 'btn btn-action' : 'btn btn-outline';
        return <li key={'page-'+number}>
            <a href="" className={classes}>{number}</a>
        </li>;
    }

    renderPageList() {
        let pageElems = [],
            page = this.props.page,
            pages = this.props.pages;


        if (pages < 7) {
            for (let i = 1; i <= pages; i++) {
                pageElems.push(this.renderPage(i));
            }
        } else {
            for (let i = 1; i <= 3; i++) {
                pageElems.push(this.renderPage(i));
            }

            if (page > 5) {
                pageElems.push(<li key="spacer-1" className="spacer">&hellip;</li>);
            }

            for (let i = (page - 1); i <= (page + 1); i++) {
                if (i > 3 && i < (pages - 2)) {
                    pageElems.push(this.renderPage(i));
                }
            }

            if (page < (pages - 4)) {
                pageElems.push(<li key="spacer-2" className="spacer">&hellip;</li>);
            }

            for (let i = (pages - 2); i <= pages; i++) {
                pageElems.push(this.renderPage(i));
            }
        }

        return <ul className="pagination-pages">{pageElems}</ul>;
    }

    renderNextPage() {
        if (this.props.page < this.props.pages) {
            return <a href="" className="btn btn-outline pagination-next">
                Next page &rarr;
            </a>;
        }

        return <div className="btn btn-outline btn-disabled pagination-next" aria-disabled="true">
            Next page &rarr;
        </div>;
    }

    render() {
        return <nav className="pagination">
            {this.renderPrevPage()}
            {this.renderPageList()}
            {this.renderNextPage()}
        </nav>;
    }
}

Pagination.propTypes = {
    pages: PropTypes.number.isRequired,
    page: PropTypes.number
};

Pagination.defaultProps = {
    page: 1
};

export default Pagination;
