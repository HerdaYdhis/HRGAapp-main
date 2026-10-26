<?php

/** @noinspection ALL */
// @formatter:off

namespace Illuminate\Contracts\View {
    /**
     * @method \Illuminate\Contracts\View\View|\Illuminate\View\View layoutData(array $data = [])
     * @method \Illuminate\Contracts\View\View|\Illuminate\View\View layout(string $view, array $params = [])
     * @method \Illuminate\Contracts\View\View|\Illuminate\View\View extends(string $view, array $params = [])
     * @method \Illuminate\Contracts\View\View|\Illuminate\View\View section(string $section)
     * @method \Illuminate\Contracts\View\View|\Illuminate\View\View slot(string $slot)
     */
    interface View {}

    /**
     * @method \Illuminate\Contracts\View\View|\Illuminate\View\View layoutData(array $data = [])
     * @method \Illuminate\Contracts\View\View|\Illuminate\View\View layout(string $view, array $params = [])
     * @method \Illuminate\Contracts\View\View|\Illuminate\View\View extends(string $view, array $params = [])
     */
    interface Factory {}
}

namespace Illuminate\View {
    /**
     * @method \Illuminate\View\View layoutData(array $data = [])
     * @method \Illuminate\View\View layout(string $view, array $params = [])
     * @method \Illuminate\View\View extends(string $view, array $params = [])
     * @method \Illuminate\View\View section(string $section)
     * @method \Illuminate\View\View slot(string $slot)
     */
    class View {}
}
