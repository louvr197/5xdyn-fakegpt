import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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
* @see \App\Http\Controllers\AskStreamController::post
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '/ask-stream',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AskStreamController::post
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AskStreamController::post
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AskStreamController::post
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
    const postForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: post.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AskStreamController::post
 * @see app/Http/Controllers/AskStreamController.php:27
 * @route '/ask-stream'
 */
        postForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: post.url(options),
            method: 'post',
        })
    
    post.form = postForm
const stream = {
    index: Object.assign(index, index),
post: Object.assign(post, post),
}

export default stream