export const useDebounce = () => {
    let timer = undefined

    return (callback: () => void, timeout: number = 300) => {
        if (timer) {
            clearTimeout(timer)
        }

        timer = setTimeout(callback, timeout)
    }
}
