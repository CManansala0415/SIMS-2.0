<template>
    <div class="d-flex flex-column justify-content-center align-items-center">
        <div class="loading-main">
            <label class="loading-main-label">Please wait...</label>
            <div class="loading"></div>
        </div>
    </div>
</template>
<style scoped>
/* From Uiverse.io by TemplateVentures */
.loading-main {
    width: 350px;
    height: 180px;
    border-radius: 10px;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-evenly;
    padding: 30px;
    box-shadow: 2px 2px 10px -5px lightgrey;
}

.loading {
    width: 100%;
    height: 10px;
    background: rgb(157, 211, 197);
    border-radius: 10px;
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 50%;
    height: 10px;
    background: rgb(40, 201, 121);
    border-radius: 10px;
    z-index: 1;
    animation: loading 0.6s alternate infinite;
}

.loading-main-label {
    color: rgb(48, 48, 48);
    font-size: 18px;
    animation: bit 0.6s alternate infinite;
}

@keyframes bit {
    from {
        opacity: 0.3;
    }

    to {
        opacity: 1;
    }
}

@keyframes loading {
    0% {
        left: -25%;
    }

    100% {
        left: 70%;
    }

    0% {
        left: -25%;
    }
}
</style>