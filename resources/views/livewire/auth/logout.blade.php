<div>
    <button type="button" wire:click="logout" wire:loading.attr="disabled"
        class="dropdown-item d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="20" height="20"
            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
            stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2">
            </path>
            <path d="M9 12h12l-3 -3"></path>
            <path d="M18 15l3 -3"></path>
        </svg>
        Logout
    </button>
</div>
