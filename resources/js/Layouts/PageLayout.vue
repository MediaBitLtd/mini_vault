<template>
    <section class="flex flex-col items-center pt-5 sm:pt-10 h-full max-w-[100dvw]">
        <header class="w-full px-5 mb-5 xl:max-w-4xl">
            <h1 class="text-3xl font-bold mb-4">
                <slot name="title" />
            </h1>
            <slot name="header" />
        </header>
        <main id="container" class="w-full h-full overflow-y-auto" style="scrollbar-gutter: stable both-edges;">
            <div class="h-full">
                <div class="xl:max-w-4xl px-5 mx-auto pb-5">
                    <slot name="content" />
                </div>
            </div>
        </main>
        <footer class="w-full xl:max-w-4xl lg:px-5 mx-auto">
            <slot name="footer" />
        </footer>
    </section>
</template>
