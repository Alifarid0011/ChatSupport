<template>
<div>

  <Multiselect
      v-model="value"
      mode="tags"
      :searchable="true"
      :createTag="true"
      :options="[
    { value: 'batman', label: 'Batman' },
    { value: 'robin', label: 'Robin' },
    { value: 'joker', label: 'Joker' },
  ]"
      class="multiselect-blue"
  />
</div>


</template>

<script>

import Multiselect from '@vueform/multiselect'
// import sel from './sel';
export default {
  name:'test',
  components: {
    Multiselect,
    // sel
  },
  data() {
    return {
      value: null,
    }
  }
}

</script>
<style src="@vueform/multiselect/themes/default.css"></style>
