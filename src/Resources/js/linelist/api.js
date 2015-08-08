import xhr from 'xhr';
import 'native-promise-only';

/**
 * Lines API
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class LinesAPI {
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
                    lines: JSON.parse(body)
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

                resolve(lineId);
                //resolve(JSON.parse(body));
            });
        });
    }
}

export default new LinesAPI();
