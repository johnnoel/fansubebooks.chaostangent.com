import React, { Component, PropTypes } from 'react';

/**
 * Line component
 *
 * @author John Noel <john.noel@rckt.co.uk>
 * @package FansubEbooks
 */
class Line extends Component {
    renderTweetLink() {
        if (this.props.tweetId !== null) {
            return <a href={`https://twitter.com/fansub_ebooks/status/${this.props.tweetId}`} className="line-tweet">Twitter</a>;
        }

        return null;
    }

    render() {
        return <div className="line">
            <p className="line-line">{this.props.line}</p>
            <div className="line-actions">
                <div className="line-voteup">
                    <button type="button">Vote up</button>
                </div>
                <div className="line-votes">
                    <span className="line-positivevotes">+{this.props.positiveVoteCount}</span> /
                    <span className="line-negativevotes">-{this.props.negativeVoteCount}</span> /
                    <span className="line-score">{this.props.positiveVoteCount - this.props.negativeVoteCount}</span>
                </div>
                <div className="line-votedown">
                    <button type="button">Vote down</button>
                </div>
                <div className="line-flag">
                    <button type="button">Flag</button>
                </div>
                <div className="line-links">
                    <a href="" className="line-permalink">Permalink</a>
                    {this.renderTweetLink()}
                </div>
            </div>
        </div>;
    }
}

Line.propTypes = {
    id: PropTypes.number.isRequired,
    line: PropTypes.string.isRequired,
    positiveVoteCount: PropTypes.number,
    negativeVoteCount: PropTypes.number,
    tweetId: PropTypes.string
};

Line.defaultProps = {
    positiveVoteCount: 0,
    negativeVoteCount: 0,
    tweetId: null
};

export default Line;
