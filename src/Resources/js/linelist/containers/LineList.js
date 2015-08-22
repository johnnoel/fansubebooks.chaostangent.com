import React, { Component, PropTypes, findDOMNode } from 'react';
import { connect } from 'react-redux';
import { voteUpLine, voteDownLine, flagLine } from '../actions';
import Line from '../components/Line';
import scrollTo from '../../scrollto.js';

/**
 * Line list container component
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class LineList extends Component {
    renderLines() {
        const { dispatch, lines } = this.props;

        return lines.map(line => {
            return <Line
                line={line}
                key={line.id}
                onVoteUp={() => dispatch(voteUpLine(line.id))}
                onVoteDown={() => dispatch(voteDownLine(line.id))}
                onFlag={() => dispatch(flagLine(line.id))}
            />;
        }).toArray();
    }

    render() {
        const { dispatch } = this.props;

        return <div>
            <div className="line-grid">
                {this.renderLines()}
            </div>
        </div>;
    }
}

LineList.propTypes = {
    lines: PropTypes.object.isRequired
};

function select(state) {
    return state;
}

export default connect(select)(LineList);
