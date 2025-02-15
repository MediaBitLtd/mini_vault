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

    fresh?: boolean;
    has_unsaved_changes?: boolean;
    _new?: boolean;
    _delete?: boolean;

    created_at?: string;
    updated_at?: string;
    deleted_at?: string;
}

export type Resource = ModelResource | {};

export interface UserResource extends ModelResource {
    first_name: string;
    last_name: string;
    email: string;

    role?: string;
    verified?: boolean;
}
