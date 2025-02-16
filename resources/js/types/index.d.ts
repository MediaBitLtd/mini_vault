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
