import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PresetController::index
 * @see app/Http/Controllers/PresetController.php:10
 * @route '/presets'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/presets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PresetController::index
 * @see app/Http/Controllers/PresetController.php:10
 * @route '/presets'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PresetController::index
 * @see app/Http/Controllers/PresetController.php:10
 * @route '/presets'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PresetController::index
 * @see app/Http/Controllers/PresetController.php:10
 * @route '/presets'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PresetController::index
 * @see app/Http/Controllers/PresetController.php:10
 * @route '/presets'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PresetController::index
 * @see app/Http/Controllers/PresetController.php:10
 * @route '/presets'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PresetController::index
 * @see app/Http/Controllers/PresetController.php:10
 * @route '/presets'
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
* @see \App\Http\Controllers\PresetController::store
 * @see app/Http/Controllers/PresetController.php:21
 * @route '/presets'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/presets',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PresetController::store
 * @see app/Http/Controllers/PresetController.php:21
 * @route '/presets'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PresetController::store
 * @see app/Http/Controllers/PresetController.php:21
 * @route '/presets'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\PresetController::store
 * @see app/Http/Controllers/PresetController.php:21
 * @route '/presets'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PresetController::store
 * @see app/Http/Controllers/PresetController.php:21
 * @route '/presets'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\PresetController::destroy
 * @see app/Http/Controllers/PresetController.php:41
 * @route '/presets/{preset}'
 */
export const destroy = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/presets/{preset}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\PresetController::destroy
 * @see app/Http/Controllers/PresetController.php:41
 * @route '/presets/{preset}'
 */
destroy.url = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { preset: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { preset: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    preset: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        preset: typeof args.preset === 'object'
                ? args.preset.id
                : args.preset,
                }

    return destroy.definition.url
            .replace('{preset}', parsedArgs.preset.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PresetController::destroy
 * @see app/Http/Controllers/PresetController.php:41
 * @route '/presets/{preset}'
 */
destroy.delete = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\PresetController::destroy
 * @see app/Http/Controllers/PresetController.php:41
 * @route '/presets/{preset}'
 */
    const destroyForm = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PresetController::destroy
 * @see app/Http/Controllers/PresetController.php:41
 * @route '/presets/{preset}'
 */
        destroyForm.delete = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const presets = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
destroy: Object.assign(destroy, destroy),
}

export default presets