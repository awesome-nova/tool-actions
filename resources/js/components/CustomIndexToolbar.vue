<template>
    <div class="flex w-full justify-end items-center mx-3">
        <button v-for="action in actions"
           :key="action.uriKey" class="btn btn-default btn-primary" @click.prevent="determineActionStrategy(action.uriKey)">{{action.label}}</button>
        <transition name="fade">
            <component
                :is="selectedAction.component"
                :working="working"
                v-if="confirmActionModalOpened"
                selected-resources="all"
                :resource-name="resourceName"
                :action="selectedAction"
                :errors="errors"
                @confirm="executeAction"
                @close="confirmActionModalOpened = false"
            />
        </transition>
    </div>
</template>

<script>
import { Errors } from 'form-backend-validation'
import  InteractsWithResourceInformation  from 'laravel-nova/src/mixins/InteractsWithResourceInformation'

export default {
    mixins: [InteractsWithResourceInformation],
    props: {
        resourceName: String,
        queryString: {
            type: Object,
            default: () => ({
                currentSearch: '',
                encodedFilters: '',
                currentTrashed: '',
                viaResource: '',
                viaResourceId: '',
                viaRelationship: '',
            }),
        },
    },

    data: () => ({
        actions: [],
        working: false,
        errors: new Errors(),
        selectedActionKey: '',
        confirmActionModalOpened: false,
    }),

    mounted() {
        this.getActions()
    },

    methods: {
        /**
         * Get the actions available for the current resource.
         */
        getActions() {
            this.actions = []
            return Nova.request()
                .get(`/nova-api/${this.resourceName}/actions`)
                .then(response => {
                    this.actions = _.filter(response.data.actions, action => {
                        return action.toolAction
                    })
                })
        },
        /**
         * Determine whether the action should redirect or open a confirmation modal
         */
        determineActionStrategy(uriKey) {
            this.selectedActionKey = uriKey

            if (this.selectedAction.withoutConfirmation) {
                this.executeAction()
            } else {
                this.openConfirmationModal()
            }
        },

        /**
         * Confirm with the user that they actually want to run the selected action.
         */
        openConfirmationModal() {
            this.confirmActionModalOpened = true
        },

        /**
         * Close the action confirmation modal.
         */
        closeConfirmationModal() {
            this.confirmActionModalOpened = false
        },

        /**
         * Initialize all of the action fields to empty strings.
         */
        initializeActionFields() {
            this.actions.forEach(action => {
                action.fields.forEach(field => {
                    field.fill = () => ''
                })
            })
        },
        /**
         * Execute the selected action.
         */
        executeAction() {
            this.working = true

            Nova.request({
                method: 'post',
                url: this.endpoint || `/nova-api/${this.resourceName}/action`,
                params: this.actionRequestQueryString,
                data: this.actionFormData(),
            })
                .then(response => {
                    this.confirmActionModalOpened = false
                    this.handleActionResponse(response.data)
                    this.working = false
                })
                .catch(error => {
                    this.working = false

                    if (error.response.status == 422) {
                        this.errors = new Errors(error.response.data.errors)
                    }
                })
        },

        /**
         * Gather the action FormData for the given action.
         */
        actionFormData() {
            return _.tap(new FormData(), formData => {
                _.each(this.selectedAction.fields, field => {
                    field.fill(formData)
                })
            })
        },

        /**
         * Handle the action response. Typically either a message, download or a redirect.
         */
        handleActionResponse(response) {
            if (response.message) {
                this.$emit('actionExecuted')
                this.$toasted.show(response.message, { type: 'success' })
            } else if (response.deleted) {
                this.$emit('actionExecuted')
            } else if (response.danger) {
                this.$emit('actionExecuted')
                this.$toasted.show(response.danger, { type: 'error' })
            } else if (response.download) {
                let link = document.createElement('a')
                link.href = response.download
                link.download = response.name
                document.body.appendChild(link)
                link.click()
                document.body.removeChild(link)
            } else if (response.redirect) {
                window.location = response.redirect
            } else {
                this.$emit('actionExecuted')
                this.$toasted.show(this.__('The action ran successfully!'), { type: 'success' })
            }
        },
    },


    computed: {
        selectedAction() {
            if (this.selectedActionKey) {
                return _.find(this.actions, a => a.uriKey == this.selectedActionKey)
            }
        },

        /**
         * Get the query string for an action request.
         */
        actionRequestQueryString() {
            return {
                action: this.selectedActionKey,
            }
        },
    }
}
</script>
