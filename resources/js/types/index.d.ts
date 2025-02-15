import { UserResource } from '~/types/resources'

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: UserResource;
    };
    isLocal: boolean;
    csrf_token: string;
};
