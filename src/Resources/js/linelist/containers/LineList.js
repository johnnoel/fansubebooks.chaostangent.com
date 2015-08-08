import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { changePage } from '../actions';
import Line from '../components/Line';
import Pagination from '../components/Pagination';

/**
 * Line list container component
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class LineList extends Component {
    renderLines(lines) {
        return <Line id={1} line="Hooray, I love miso!" positiveVoteCount={3} negativeVoteCount={0} tweetId="285279211990704129" />;
    }

    render() {
        const { dispatch, page, pages, lines } = this.props;

        return <div>
            <div className="line-grid">
                {this.renderLines()}
            </div>
            <Pagination pages={pages} page={page} onPageClick={page => dispatch(changePage(page))} />
        </div>;
    }
}

LineList.propTypes = {
    page: PropTypes.number.isRequired,
    pages: PropTypes.number.isRequired,
    lines: PropTypes.array
};

LineList.defaultProps = {
    lines: []
};

function select(state) {
    return {
        page: state.page,
        pages: state.pages,
        lines: state.lines
    };
}

export default connect(select)(LineList);
