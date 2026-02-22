import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\CustomInstructionsController::edit
 * @see app/Http/Controllers/CustomInstructionsController.php:14
 * @route '/custom-instructions'
 */
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/custom-instructions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CustomInstructionsController::edit
 * @see app/Http/Controllers/CustomInstructionsController.php:14
 * @route '/custom-instructions'
 */
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CustomInstructionsController::edit
 * @see app/Http/Controllers/CustomInstructionsController.php:14
 * @route '/custom-instructions'
 */
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CustomInstructionsController::edit
 * @see app/Http/Controllers/CustomInstructionsController.php:14
 * @route '/custom-instructions'
 */
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CustomInstructionsController::edit
 * @see app/Http/Controllers/CustomInstructionsController.php:14
 * @route '/custom-instructions'
 */
    const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CustomInstructionsController::edit
 * @see app/Http/Controllers/CustomInstructionsController.php:14
 * @route '/custom-instructions'
 */
        editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CustomInstructionsController::edit
 * @see app/Http/Controllers/CustomInstructionsController.php:14
 * @route '/custom-instructions'
 */
        editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
/**
* @see \App\Http\Controllers\CustomInstructionsController::update
 * @see app/Http/Controllers/CustomInstructionsController.php:37
 * @route '/custom-instructions'
 */
export const update = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/custom-instructions',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\CustomInstructionsController::update
 * @see app/Http/Controllers/CustomInstructionsController.php:37
 * @route '/custom-instructions'
 */
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CustomInstructionsController::update
 * @see app/Http/Controllers/CustomInstructionsController.php:37
 * @route '/custom-instructions'
 */
update.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\CustomInstructionsController::update
 * @see app/Http/Controllers/CustomInstructionsController.php:37
 * @route '/custom-instructions'
 */
    const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url({
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CustomInstructionsController::update
 * @see app/Http/Controllers/CustomInstructionsController.php:37
 * @route '/custom-instructions'
 */
        updateForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
const customInstructions = {
    edit: Object.assign(edit, edit),
update: Object.assign(update, update),
}

export default customInstructions