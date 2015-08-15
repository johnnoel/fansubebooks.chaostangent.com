import map from 'lodash/collection/map';
import xhr from 'xhr';
import 'native-promise-only';
import LocalStorage from '../localstorage/localstorage';

/**
 * Lines API
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class LinesAPI {
    constructor() {
        this.storage = new LocalStorage('fansubebooks_');
    }

    /**
     * Get the next set of lines
     *
     * @param number page
     * @return Promise
     */
    getLines(page) {
        return new Promise((resolve, reject) => {
            xhr({
                uri: Routing.generate('popular', { page: page, _format: 'json' })
            }, (err, resp, body) => {
                if (err) {
                    reject(err);
                    return;
                }

                resolve({
                    page: page,
                    lines: this.mergeUserVotes(JSON.parse(body))
                });
            });
        });
    }

    /**
     * Vote a line up
     *
     * @param number lineId
     * @return Promise
     */
    voteUp(lineId) {
        return new Promise((resolve, reject) => {
            xhr({
                method: 'POST',
                uri: Routing.generate('line_voteup', { id: lineId, _format: 'json' })
            }, (err, resp, body) => {
                if (err) {
                    reject(err);
                    return;
                }

                this.updateUserVotes(lineId, 'positive');
                resolve(lineId);
                //resolve(JSON.parse(body));
            });
        });
    }

    /**
     * Vote a line down
     *
     * @param number lineId
     * @return Promise
     */
    voteDown(lineId) {
        return new Promise((resolve, reject) => {
            xhr({
                method: 'POST',
                uri: Routing.generate('line_votedown', { id: lineId, _format: 'json' })
            }, (err, resp, body) => {
                if (err) {
                    reject(err);
                    return;
                }

                this.updateUserVotes(lineId, 'negative');
                resolve(lineId);
                //resolve(JSON.parse(body));
            });
        });
    }

    /**
     * Flag a line as inappropriate
     *
     * @param number lineId
     * @return Promise
     */
    flag(lineId) {
        return new Promise((resolve, reject) => {
            xhr({
                method: 'POST',
                uri: Routing.generate('line_flag', { id: lineId, _format: 'json' })
            }, (err, resp, body) => {
                if (err) {
                    reject(err);
                    return;
                }

                this.updateUserVotes(lineId, 'flag');
                resolve(lineId);
                //resolve(JSON.parse(body));
            });
        });
    }

    /**
     * Get the IDs of lines that a user has interacted with
     *
     * @param string what "positive", "negative" or "flag"
     * @return array
     */
    getUserVotes(what) {
        if (['positive','negative','flag'].indexOf(what) === -1) {
            throw '"what" must be "positive", "negative" or "flag"';
        }

        return this.storage.get(what+'_votes', []);
    }

    /**
     * Update the IDs of lines that a user has interacted with
     *
     * @param number lineId The ID of the line
     * @param string what "positive", "negative" or "flag"
     */
    updateUserVotes(lineId, what) {
        if (['positive','negative','flag'].indexOf(what) === -1) {
            throw '"what" must be "positive", "negative" or "flag"';
        }

        if (typeof lineId != 'number') {
            lineId = parseInt(lineId, 10);
        }

        let votes = this.storage.get(what+'_votes', []);
        votes.unshift(lineId);

        if (votes.length > 1000) {
            votes = votes.slice(0, 999);
        }

        this.storage.set(what+'_votes', votes);
    }

    /**
     * Given an array of line objects, merge in the user vote status, i.e.
     * whether a user has voted positively, negatively or flagged a line
     *
     * @param array lines
     * @return array
     */
    mergeUserVotes(lines) {
        let p = this.getUserVotes('positive'),
            n = this.getUserVotes('negative'),
            f = this.getUserVotes('flag');

        return map(lines, line => {
            line.user_positive_vote = (p.indexOf(line.id) !== -1);
            line.user_negative_vote = (n.indexOf(line.id) !== -1);
            line.user_flag = (f.indexOf(line.id) !== -1);

            return line;
        });
    }
}

export default new LinesAPI();
