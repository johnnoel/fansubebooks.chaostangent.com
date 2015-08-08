/**
 * Promise middleware
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @see https://github.com/pburtchaell/redux-promise-middleware/blob/master/src/index.js
 */
export default function promiseMiddleware() {
    return next => action => {
        if (!action || typeof action.payload.then !== 'function') {
            return next(action);
        }

        const promise = action.payload;
        const [ PENDING, FULFILLED, REJECTED ] = action.types;

        next({
            type: PENDING
        });

        return promise.then(
            payload => next({
                type: FULFILLED,
                payload,
            }), error => next({
                type: REJECTED,
                payload: error,
                error: true
            })
        );
    }
}
