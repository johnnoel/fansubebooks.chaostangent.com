import xhr from 'xhr';
import 'native-promise-only';

/**
 * Lines API
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
export default class LinesAPI {
    getLines(page) {
        return new Promise((resolve, reject) => {
            xhr({
                uri: Routing.generate('popular', { page: page, _format: 'json' }),
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
}
