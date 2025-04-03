export type DDate = `${ number }-${ number }-${ number }`;
export type DTime = `${ number }:${ number }` | `${ number }:${ number }:${ number }`;
export type DateTime = `${ DDate } ${ DTime }`;

export interface CollectionMetaResource {
    page: number;
    per_page: number;
    last_page: number;
    total: number;
}

export interface CollectionResource<T extends Resource> {
    items: T[],
    meta?: CollectionMetaResource,
}

export interface ModelResource {
    id: number;

    _fresh?: boolean;
    _hasUnsavedChanges?: boolean;
    _new?: boolean;
    _delete?: boolean;

    created_at?: DateTime;
    updated_at?: DateTime;
    deleted_at?: DateTime;
}

export type Resource = ModelResource | {};

export interface UserResource extends ModelResource {
    first_name: string;
    last_name: string;
    email: string;
    email_verified_at: DateTime
    timezone: string;
    onboard: boolean;

    role?: string;
    verified?: boolean;
}

export interface CategoryResource extends ModelResource {
    name: string;
    icon: string;
    fields: FieldResource[];
}

export interface FieldResource extends ModelResource {
    slug: string;
    label: string;
    sensitive: boolean;
    type: string; // TODO change this to enum
}

export interface VaultResource extends ModelResource {
    name: string;
    is_unlockable: boolean;
}

export interface VaultRecordResource extends ModelResource {
    name: string;
    is_favourite: boolean;
    category: CategoryResource;
    values: VaultRecordValueResource[];
}

export interface VaultRecordValueResource extends ModelResource {
    name?: string;
    value?: string;
    invalid?: boolean;
    field?: FieldResource;
}
