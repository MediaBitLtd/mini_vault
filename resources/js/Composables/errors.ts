import { AxiosError, AxiosResponse, isAxiosError } from 'axios'
import { useToast } from 'vue-toastification'

export interface ConfigOptions {
    showToast: boolean;
}

export interface ExpectedErrorBody {
    message?: string;
}

export interface HandledError {
    status: number;
    errorMessage: string;
    errors?: { [k: string]: string[] };
    error: AxiosError;
}

/**
 * Error handler composable
 */
export function useErrorHandler() {
    const toast = useToast()

    /**
     * Gets the error message from an axios response
     * @param response
     */
    function getErrorMessage(response: AxiosResponse<ExpectedErrorBody>) {
        switch (response.status) {
            case 400:
                return response.data.message || 'Something went wrong, please try again later'
            case 401:
                return response.data.message || 'Unauthenticated'
            case 403:
                return response.data.message || 'Unauthorised'
            case 404:
                const index = response.data.message?.indexOf('App\\Models\\')
                if (index !== -1) {
                    return response.data.message.substring(
                        index + 'App\\Models\\'.length,
                        response.data.message.lastIndexOf(']')
                    ) + ' not found'
                }
                return response.data.message || 'Not found'
            case 422:
                return response.data.message
            case 423:
                return 'Company subscription is expired. Please contact your administrator.'
            case 429:
                return 'Too many request attempts. Please try again later.'
            case 500:
                return 'Server error'
            case 502:
                return response.data.message || 'Service not available'

            default:
                return 'Something went wrong, please try again later'
        }
    }

    /**
     * Handles api errors
     *
     * @param err
     * @param options
     */
    function handleAPIError(
        err: unknown,
        options: ConfigOptions = {
            showToast: true,
        }
    ): HandledError {
        if (!isAxiosError(err)) {
            if (options.showToast) {
                toast.error('Something went wrong')
            }
            throw err
        }

        const response = err.response

        if (!response) {
            // We didn't get any response from the server
            if (options.showToast) {
                toast.error('Unable to connect to the server. Please try again later.')
            }

            return {
                status: 502,
                errorMessage: 'Unable to connect to the server. Please try again later.',
                error: err,
            }
        }

        if (response.status === 401) {
            window.location.href = '/auth/logout'
        }

        const message = getErrorMessage(response)

        if (options.showToast) {
            toast.error(message)
        }

        return {
            status: response.status,
            errorMessage: message,
            errors: response.data?.errors,
            error: err,
        }
    }

    return {
        getErrorMessage,
        handleAPIError,
    }
}
