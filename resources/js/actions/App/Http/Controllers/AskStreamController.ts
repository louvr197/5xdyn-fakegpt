import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AskStreamController::index
 * @see app/Http/Controllers/AskStreamController.php:20
 * @route '/ask-stream'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/ask-stream',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AskStreamController::index
 * @see app/Http/Controllers/AskStreamController.php:20
 * @route '/ask-stream'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AskStreamController::index
 * @see app/Http/Controllers/AskStreamController.php:20
 * @route '/ask-stream'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AskStreamController::index
 * @see app/Http/Controllers/AskStreamController.php:20
 * @route '/ask-stream'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AskStreamController::index
 * @see app/Http/Controllers/AskStreamController.php:20
 * @route '/ask-stream'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AskStreamController::index
 * @see app/Http/Controllers/AskStreamController.php:20
 * @route '/ask-stream'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AskStreamController::index
 * @see app/Http/Controllers/AskStreamController.php:20
 * @route '/ask-stream'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\AskStreamController::stream
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
export const stream = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stream.url(options),
    method: 'post',
})

stream.definition = {
    methods: ["post"],
    url: '/ask-stream',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AskStreamController::stream
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
stream.url = (options?: RouteQueryOptions) => {
    return stream.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AskStreamController::stream
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
stream.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stream.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AskStreamController::stream
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
    const streamForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: stream.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AskStreamController::stream
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
        streamForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: stream.url(options),
            method: 'post',
        })
    
    stream.form = streamForm
const AskStreamController = { index, stream }

export default AskStreamController