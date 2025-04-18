import jsSHA from "jssha";

const bufferToBase64URLString = (buffer: ArrayBuffer, basic = false): string => {
    // @ts-ignore
    const str = String.fromCharCode(...new Uint8Array(buffer));
    return basic ? btoa(str) : btoa(str)
        .replaceAll('+', '-')
        .replaceAll('/', '_')
        .replace('=', '')
}
const base64URLStringToBuffer = (base64URLString: string): ArrayBuffer => {
    // Convert from Base64URL to Base64
    const base64 = base64URLString.replace(/-/g, '+').replace(/_/g, '/');
    /**
     * Pad with '=' until it's a multiple of four
     * (4 - (85 % 4 = 1) = 3) % 4 = 3 padding
     * (4 - (86 % 4 = 2) = 2) % 4 = 2 padding
     * (4 - (87 % 4 = 3) = 1) % 4 = 1 padding
     * (4 - (88 % 4 = 0) = 4) % 4 = 0 padding
     */
    const padLength = (4 - (base64.length % 4)) % 4;
    const padded = base64.padEnd(base64.length + padLength, '=');

    // Convert to a binary string
    const binary = atob(padded);

    // Convert binary string to buffer
    const buffer = new ArrayBuffer(binary.length);
    const bytes = new Uint8Array(buffer);

    for (let i = 0; i < binary.length; i++) {
        bytes[i] = binary.charCodeAt(i);
    }

    return buffer;
}

const generatePassword = ({
    length = 12,
    includeLetters = true,
    includeNumbers = true,
    includeSymbols = true,
} = {}) => {
    let charSet = ''
    let newPassword = ''

    if (includeLetters) {
        charSet += Array.from({ length: 26 }, (_, i) =>
            String.fromCharCode(97 + i)
        ).join('') + Array.from({ length: 26 }, (_, i) =>
            String.fromCharCode(65 + i)
        ).join('')
    }

    if(includeNumbers) {
        charSet += Array.from({ length: 10 }, (_, i) =>
            String.fromCharCode(48 + i)
        ).join('')
    }

    if (includeSymbols) {
        charSet += '!$@#%&?^'
    }

    if (charSet.length > 0) {
        const randomValues = crypto.getRandomValues(new Uint32Array(length))
        for (let i = 0; i < length; i++) {
            newPassword += charSet[randomValues[i] % charSet.length]
        }
    }

    return newPassword
}


export const useEncryption = () => {
    return {
        bufferToBase64URLString,
        base64URLStringToBuffer,
        getOTPFromSecret,
        generatePassword,
    }
}

// Code extracted from https://jsfiddle.net/russau/ch8PK/ < Thank you!

export const dec2hex = (val: number): string => (val < 15.5 ? '0' : '') + Math.round(val).toString(16)
export const hex2dec = (val: string): number => parseInt(val, 16)

export const leftpad = (str, len, pad) => len + 1 >= str.length
    ? Array(len + 1 - str.length).join(pad) + str
    : str

export const base32tohex = (str) => {
    const base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'
    let bits = ''
    let hex = ''

    for (let i = 0; i < str.length; i++) {
        const val = base32chars.indexOf(str.charAt(i).toUpperCase())
        bits += leftpad(val.toString(2), 5, '0')
    }

    for (let i = 0; i + 4 <= bits.length; i += 4) {
        const chunk = bits.substr(i, 4)
        hex = hex + parseInt(chunk, 2).toString(16)
    }

    return hex
}

export const getOTPFromSecret = (secret, period = 30) => {
    try {
        const time = Math.floor(Math.floor((new Date).getTime() / 1000) / period)
        const paddedTime = leftpad(dec2hex(time), 16, '0')

        const shaObj = new jsSHA('SHA-1', 'HEX')
        shaObj.setHMACKey(base32tohex(secret), 'HEX')
        shaObj.update(paddedTime)

        const hmac = shaObj.getHMAC('HEX')

        const offset = hex2dec(hmac.substring(hmac.length - 1))
        const otp = (hex2dec(hmac.substring(offset * 2, offset * 2 + 8)) & hex2dec('7fffffff')) + ''

        return otp.substring(otp.length - 6, otp.length)
    } catch (e) {
        return ''
    }
}
