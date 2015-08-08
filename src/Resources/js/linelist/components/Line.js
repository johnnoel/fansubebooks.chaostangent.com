import React, { Component, PropTypes } from 'react';

/**
 * Line component
 *
 * @author John Noel <john.noel@chaostangent.com>
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
        const line = this.props.line;

        return <div className="line">
            <p className="line-line">{line.line}</p>
            <div className="line-actions">
                <div className="line-voteup">
                    <button type="button" onClick={() => this.props.onVoteUp()}>Vote up</button>
                </div>
                <div className="line-votes">
                    <span className="line-positivevotes">+{line.positive_vote_count}</span> /
                    <span className="line-negativevotes">-{line.negative_vote_count}</span> /
                    <span className="line-score">{line.positive_vote_count - line.negative_vote_count}</span>
                </div>
                <div className="line-votedown">
                    <button type="button" onClick={() => this.props.onVoteDown()}>Vote down</button>
                </div>
                <div className="line-flag">
                    <button type="button" onClick={() => this.props.onFlag()}>Flag</button>
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
    line: PropTypes.shape({
        id: PropTypes.number.isRequired,
        line: PropTypes.string.isRequired,
        positive_vote_count: PropTypes.number,
        negative_vote_count: PropTypes.number,
        tweet_id: PropTypes.string
    }).isRequired,
    onVoteUp: PropTypes.func,
    onVoteDown: PropTypes.func,
    onFlag: PropTypes.func
};

Line.defaultProps = {
    onVoteUp: () => {},
    onVoteDown: () => {},
    onFlag: () => {}
};

export default Line;
