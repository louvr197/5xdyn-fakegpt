import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AskController::index
 * @see app/Http/Controllers/AskController.php:13
 * @route '/ask'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/ask',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AskController::index
 * @see app/Http/Controllers/AskController.php:13
 * @route '/ask'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AskController::index
 * @see app/Http/Controllers/AskController.php:13
 * @route '/ask'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AskController::index
 * @see app/Http/Controllers/AskController.php:13
 * @route '/ask'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AskController::index
 * @see app/Http/Controllers/AskController.php:13
 * @route '/ask'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AskController::index
 * @see app/Http/Controllers/AskController.php:13
 * @route '/ask'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AskController::index
 * @see app/Http/Controllers/AskController.php:13
 * @route '/ask'
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
* @see \App\Http\Controllers\AskController::ask
 * @see app/Http/Controllers/AskController.php:21
 * @route '/ask'
 */
export const ask = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ask.url(options),
    method: 'post',
})

ask.definition = {
    methods: ["post"],
    url: '/ask',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AskController::ask
 * @see app/Http/Controllers/AskController.php:21
 * @route '/ask'
 */
ask.url = (options?: RouteQueryOptions) => {
    return ask.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AskController::ask
 * @see app/Http/Controllers/AskController.php:21
 * @route '/ask'
 */
ask.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ask.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AskController::ask
 * @see app/Http/Controllers/AskController.php:21
 * @route '/ask'
 */
    const askForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: ask.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AskController::ask
 * @see app/Http/Controllers/AskController.php:21
 * @route '/ask'
 */
        askForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: ask.url(options),
            method: 'post',
        })
    
    ask.form = askForm
const AskController = { index, ask }

export default AskController