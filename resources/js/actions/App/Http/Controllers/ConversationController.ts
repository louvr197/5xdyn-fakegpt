import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ConversationController::index
 * @see app/Http/Controllers/ConversationController.php:18
 * @route '/conversations'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/conversations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ConversationController::index
 * @see app/Http/Controllers/ConversationController.php:18
 * @route '/conversations'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::index
 * @see app/Http/Controllers/ConversationController.php:18
 * @route '/conversations'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ConversationController::index
 * @see app/Http/Controllers/ConversationController.php:18
 * @route '/conversations'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ConversationController::index
 * @see app/Http/Controllers/ConversationController.php:18
 * @route '/conversations'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ConversationController::index
 * @see app/Http/Controllers/ConversationController.php:18
 * @route '/conversations'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ConversationController::index
 * @see app/Http/Controllers/ConversationController.php:18
 * @route '/conversations'
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
* @see \App\Http\Controllers\ConversationController::create
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/conversations/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ConversationController::create
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::create
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ConversationController::create
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ConversationController::create
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ConversationController::create
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ConversationController::create
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/create'
 */
        createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    create.form = createForm
/**
* @see \App\Http\Controllers\ConversationController::store
 * @see app/Http/Controllers/ConversationController.php:42
 * @route '/conversations'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/conversations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ConversationController::store
 * @see app/Http/Controllers/ConversationController.php:42
 * @route '/conversations'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::store
 * @see app/Http/Controllers/ConversationController.php:42
 * @route '/conversations'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ConversationController::store
 * @see app/Http/Controllers/ConversationController.php:42
 * @route '/conversations'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ConversationController::store
 * @see app/Http/Controllers/ConversationController.php:42
 * @route '/conversations'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\ConversationController::show
 * @see app/Http/Controllers/ConversationController.php:64
 * @route '/conversations/{conversation}'
 */
export const show = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/conversations/{conversation}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ConversationController::show
 * @see app/Http/Controllers/ConversationController.php:64
 * @route '/conversations/{conversation}'
 */
show.url = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { conversation: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    conversation: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        conversation: typeof args.conversation === 'object'
                ? args.conversation.id
                : args.conversation,
                }

    return show.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::show
 * @see app/Http/Controllers/ConversationController.php:64
 * @route '/conversations/{conversation}'
 */
show.get = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ConversationController::show
 * @see app/Http/Controllers/ConversationController.php:64
 * @route '/conversations/{conversation}'
 */
show.head = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ConversationController::show
 * @see app/Http/Controllers/ConversationController.php:64
 * @route '/conversations/{conversation}'
 */
    const showForm = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ConversationController::show
 * @see app/Http/Controllers/ConversationController.php:64
 * @route '/conversations/{conversation}'
 */
        showForm.get = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ConversationController::show
 * @see app/Http/Controllers/ConversationController.php:64
 * @route '/conversations/{conversation}'
 */
        showForm.head = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\ConversationController::edit
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/{conversation}/edit'
 */
export const edit = (args: { conversation: string | number } | [conversation: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/conversations/{conversation}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ConversationController::edit
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/{conversation}/edit'
 */
edit.url = (args: { conversation: string | number } | [conversation: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    conversation: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        conversation: args.conversation,
                }

    return edit.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::edit
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/{conversation}/edit'
 */
edit.get = (args: { conversation: string | number } | [conversation: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ConversationController::edit
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/{conversation}/edit'
 */
edit.head = (args: { conversation: string | number } | [conversation: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ConversationController::edit
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/{conversation}/edit'
 */
    const editForm = (args: { conversation: string | number } | [conversation: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ConversationController::edit
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/{conversation}/edit'
 */
        editForm.get = (args: { conversation: string | number } | [conversation: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ConversationController::edit
 * @see app/Http/Controllers/ConversationController.php:0
 * @route '/conversations/{conversation}/edit'
 */
        editForm.head = (args: { conversation: string | number } | [conversation: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
/**
* @see \App\Http\Controllers\ConversationController::update
 * @see app/Http/Controllers/ConversationController.php:99
 * @route '/conversations/{conversation}'
 */
export const update = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/conversations/{conversation}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\ConversationController::update
 * @see app/Http/Controllers/ConversationController.php:99
 * @route '/conversations/{conversation}'
 */
update.url = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { conversation: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    conversation: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        conversation: typeof args.conversation === 'object'
                ? args.conversation.id
                : args.conversation,
                }

    return update.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::update
 * @see app/Http/Controllers/ConversationController.php:99
 * @route '/conversations/{conversation}'
 */
update.put = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\ConversationController::update
 * @see app/Http/Controllers/ConversationController.php:99
 * @route '/conversations/{conversation}'
 */
update.patch = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\ConversationController::update
 * @see app/Http/Controllers/ConversationController.php:99
 * @route '/conversations/{conversation}'
 */
    const updateForm = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ConversationController::update
 * @see app/Http/Controllers/ConversationController.php:99
 * @route '/conversations/{conversation}'
 */
        updateForm.put = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\ConversationController::update
 * @see app/Http/Controllers/ConversationController.php:99
 * @route '/conversations/{conversation}'
 */
        updateForm.patch = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\ConversationController::destroy
 * @see app/Http/Controllers/ConversationController.php:232
 * @route '/conversations/{conversation}'
 */
export const destroy = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/conversations/{conversation}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ConversationController::destroy
 * @see app/Http/Controllers/ConversationController.php:232
 * @route '/conversations/{conversation}'
 */
destroy.url = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { conversation: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    conversation: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        conversation: typeof args.conversation === 'object'
                ? args.conversation.id
                : args.conversation,
                }

    return destroy.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::destroy
 * @see app/Http/Controllers/ConversationController.php:232
 * @route '/conversations/{conversation}'
 */
destroy.delete = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\ConversationController::destroy
 * @see app/Http/Controllers/ConversationController.php:232
 * @route '/conversations/{conversation}'
 */
    const destroyForm = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ConversationController::destroy
 * @see app/Http/Controllers/ConversationController.php:232
 * @route '/conversations/{conversation}'
 */
        destroyForm.delete = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\ConversationController::updateTitle
 * @see app/Http/Controllers/ConversationController.php:196
 * @route '/conversations/{conversation}/title'
 */
export const updateTitle = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateTitle.url(args, options),
    method: 'patch',
})

updateTitle.definition = {
    methods: ["patch"],
    url: '/conversations/{conversation}/title',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\ConversationController::updateTitle
 * @see app/Http/Controllers/ConversationController.php:196
 * @route '/conversations/{conversation}/title'
 */
updateTitle.url = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { conversation: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    conversation: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        conversation: typeof args.conversation === 'object'
                ? args.conversation.id
                : args.conversation,
                }

    return updateTitle.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::updateTitle
 * @see app/Http/Controllers/ConversationController.php:196
 * @route '/conversations/{conversation}/title'
 */
updateTitle.patch = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateTitle.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\ConversationController::updateTitle
 * @see app/Http/Controllers/ConversationController.php:196
 * @route '/conversations/{conversation}/title'
 */
    const updateTitleForm = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateTitle.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ConversationController::updateTitle
 * @see app/Http/Controllers/ConversationController.php:196
 * @route '/conversations/{conversation}/title'
 */
        updateTitleForm.patch = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateTitle.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateTitle.form = updateTitleForm
/**
* @see \App\Http\Controllers\ConversationController::updateCustomInstructions
 * @see app/Http/Controllers/ConversationController.php:214
 * @route '/conversations/{conversation}/custom-instructions'
 */
export const updateCustomInstructions = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCustomInstructions.url(args, options),
    method: 'patch',
})

updateCustomInstructions.definition = {
    methods: ["patch"],
    url: '/conversations/{conversation}/custom-instructions',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\ConversationController::updateCustomInstructions
 * @see app/Http/Controllers/ConversationController.php:214
 * @route '/conversations/{conversation}/custom-instructions'
 */
updateCustomInstructions.url = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { conversation: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    conversation: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        conversation: typeof args.conversation === 'object'
                ? args.conversation.id
                : args.conversation,
                }

    return updateCustomInstructions.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::updateCustomInstructions
 * @see app/Http/Controllers/ConversationController.php:214
 * @route '/conversations/{conversation}/custom-instructions'
 */
updateCustomInstructions.patch = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCustomInstructions.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\ConversationController::updateCustomInstructions
 * @see app/Http/Controllers/ConversationController.php:214
 * @route '/conversations/{conversation}/custom-instructions'
 */
    const updateCustomInstructionsForm = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateCustomInstructions.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ConversationController::updateCustomInstructions
 * @see app/Http/Controllers/ConversationController.php:214
 * @route '/conversations/{conversation}/custom-instructions'
 */
        updateCustomInstructionsForm.patch = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateCustomInstructions.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateCustomInstructions.form = updateCustomInstructionsForm
/**
* @see \App\Http\Controllers\ConversationController::regenerateTitle
 * @see app/Http/Controllers/ConversationController.php:138
 * @route '/conversations/{conversation}/regenerate-title'
 */
export const regenerateTitle = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: regenerateTitle.url(args, options),
    method: 'post',
})

regenerateTitle.definition = {
    methods: ["post"],
    url: '/conversations/{conversation}/regenerate-title',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ConversationController::regenerateTitle
 * @see app/Http/Controllers/ConversationController.php:138
 * @route '/conversations/{conversation}/regenerate-title'
 */
regenerateTitle.url = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { conversation: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    conversation: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        conversation: typeof args.conversation === 'object'
                ? args.conversation.id
                : args.conversation,
                }

    return regenerateTitle.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConversationController::regenerateTitle
 * @see app/Http/Controllers/ConversationController.php:138
 * @route '/conversations/{conversation}/regenerate-title'
 */
regenerateTitle.post = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: regenerateTitle.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ConversationController::regenerateTitle
 * @see app/Http/Controllers/ConversationController.php:138
 * @route '/conversations/{conversation}/regenerate-title'
 */
    const regenerateTitleForm = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: regenerateTitle.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ConversationController::regenerateTitle
 * @see app/Http/Controllers/ConversationController.php:138
 * @route '/conversations/{conversation}/regenerate-title'
 */
        regenerateTitleForm.post = (args: { conversation: number | { id: number } } | [conversation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: regenerateTitle.url(args, options),
            method: 'post',
        })
    
    regenerateTitle.form = regenerateTitleForm
const ConversationController = { index, create, store, show, edit, update, destroy, updateTitle, updateCustomInstructions, regenerateTitle }

export default ConversationController