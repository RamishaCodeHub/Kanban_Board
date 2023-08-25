import { createStore } from 'vuex';

const store = createStore({
  state: {
    isLoggedIn: localStorage.getItem('isLoggedIn') === 'true' || false,
  },
  mutations: {
    setIsLoggedIn(state, value) {
      state.isLoggedIn = value;
      localStorage.setItem('isLoggedIn', value.toString()); // Store in localStorage
    },
    logout(state) {
      state.isLoggedIn = false;
      localStorage.removeItem('isLoggedIn'); // Remove from localStorage on logout
    },
  },
  getters: {
    isLoggedIn(state) {
      return state.isLoggedIn;
    },
  },
});

export default store;
