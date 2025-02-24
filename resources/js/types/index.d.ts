import { UserResource } from '~/types/resources'

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: UserResource;
    };
    app: {
        isLocal: boolean;
        version: string;
    }
    csrf_token: string;
};

export interface NavigatorCredential extends Credential {
    rawId: ArrayBuffer;
    getClientExtensionResults: () => unknown;
    authenticatorAttachment: () => unknown;
    response: {
        getPublicKey: () => ArrayBuffer;
        getAuthenticatorData: () => ArrayBuffer;
        clientDataJSON: ArrayBuffer;
        getTransports: () => string[];
    }
}
