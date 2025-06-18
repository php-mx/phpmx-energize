<template>
  <div>
    {{ name }}
    <input v-model="name" type="text" />
  </div>
</template>

<script>
export default {
  setup() {
    return {
      name: "André Ricardo",
    };
  },
};
</script>

<style lang="css" scoped></style>
