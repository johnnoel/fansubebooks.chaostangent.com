import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import map from 'lodash/collection/map';
import { voteUpLine, voteDownLine, flagLine, changePage } from '../actions';
import Line from '../components/Line';
import Pagination from '../components/Pagination';

/**
 * Line list container component
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class LineList extends Component {
    renderLines() {
        const { dispatch, lines } = this.props;

        return map(lines, (line) => {
            return <Line
                line={line}
                key={line.id}
                onVoteUp={() => dispatch(voteUpLine(line.id))}
                onVoteDown={() => dispatch(voteDownLine(line.id))}
                onFlag={() => dispatch(flagLine(line.id))}
            />;
        });
    }

    render() {
        const { dispatch, page, pages } = this.props;

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
