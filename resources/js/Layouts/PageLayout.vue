<template>
    <section class="flex flex-col items-center pt-5 sm:pt-10 h-full">
        <header class="w-full px-5 mb-5 xl:max-w-4xl">
            <h1 class="text-3xl font-bold mb-10">
                <slot name="title" />
            </h1>
            <slot name="header" />
        </header>
        <main class="w-full h-full overflow-y-auto">
            <div class="h-full">
                <div class="xl:max-w-4xl px-5 mx-auto pb-10">
                    <slot name="content" />
                </div>
            </div>
        </main>
    </section>
</template>
