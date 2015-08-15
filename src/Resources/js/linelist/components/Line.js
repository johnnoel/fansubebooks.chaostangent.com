import React, { Component, PropTypes } from 'react';

/**
 * Line component
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class Line extends Component {
    renderVoteUp() {
        let { line } = this.props;
        let classes = [ 'line-voteup' ];

        if (line.user_positive_vote) {
            classes.push('active');
        }

        return <div className={classes.join(' ')}>
            <button type="button" onClick={() => this.props.onVoteUp()}>Vote up</button>
        </div>;
    }

    renderVoteDown() {
        let { line } = this.props;
        let classes = [ 'line-votedown' ];

        if (line.user_negative_vote) {
            classes.push('active');
        }

        return <div className={classes.join(' ')}>
            <button type="button" onClick={() => this.props.onVoteDown()}>Vote down</button>
        </div>;
    }

    renderFlag() {
        let { line } = this.props;
        let classes = [ 'line-flag' ];

        if (line.user_flag) {
            classes.push('active');
        }

        return <div className={classes.join(' ')}>
            <button type="button" onClick={() => this.props.onFlag()}>Flag</button>
        </div>;
    }

    renderTweetLink() {
        let { line } = this.props;

        if (line.tweet_id) {
            return <a href={`https://twitter.com/fansub_ebooks/status/${line.tweet_id}`} className="line-tweet">Twitter</a>;
        }

        return null;
    }

    render() {
        let { line } = this.props;

        return <div className="line">
            <p className="line-line">{line.line}</p>
            <div className="line-actions">
                {this.renderVoteUp()}
                <div className="line-votes">
                    <span className="line-positivevotes">+{line.positive_vote_count}</span>{' / '}
                    <span className="line-negativevotes">-{line.negative_vote_count}</span>{' / '}
                    <span className="line-score">{line.positive_vote_count - line.negative_vote_count}</span>
                </div>
                {this.renderVoteDown()}
                {this.renderFlag()}
                <div className="line-links">
                    <a href={Routing.generate('line', { id: line.id })} className="line-permalink">Permalink</a>
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
