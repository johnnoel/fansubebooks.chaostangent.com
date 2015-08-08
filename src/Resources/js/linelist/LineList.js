import React, { Component } from 'react';
import Line from './components/Line';
import Pagination from './components/Pagination';

/**
 * Line list container component
 *
 * @author John Noel <john.noel@rckt.co.uk>
 * @package FansubEbooks
 */
export default class LineList extends Component {
    render() {
        return <div>
            <div className="line-grid">
                <Line id={1} line="Hooray, I love miso!" positiveVoteCount={3} negativeVoteCount={0} tweetId="285279211990704129" />
            </div>
            <Pagination pages={3413} page={3413} />
        </div>;
    }
}

React.render(<LineList />, document.getElementById('linelist'));
