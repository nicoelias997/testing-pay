import {reactive} from 'vue'
/**
 * Utilities for the application.
 *
 * Can be installed via vue.use(utils)
 * Can be injected in components with injection
 * Can access to individual functions using import { fn } from '~/plugins/utils'
 */

/**
 * Converts an array of HTMLInputElement or HTMLCollection into an object.
 *
 * @param  {array} inputs
 * @return {any}
 */
export const formToObject = inputs => {
  return [].reduce.call(inputs, (obj, input) => {
    if (input instanceof HTMLInputElement && ![ 'button', 'image', 'submit', 'reset' ].includes(input.type)) {
      const key = input.id ?? input.name
      if (key) {
        obj[key] = input.type === 'checkbox' ? input.checked : input.value
      }
    }
    return obj
  }, {})
}

/**
 * Returns navigator language
 *
 * @return {string}
 */
export const locale = () => {
  let lang = document.querySelector('html').lang || window.navigator.language
  if (lang.indexOf('-') !== -1) lang = lang.split('-')[1]
  if (lang.indexOf('_') !== -1) lang = lang.split('_')[1]
  return lang.toLowerCase()
}

/**
 * Capitalizes string
 *
 * @param  {string} str
 * @return {string}
 */
export const capitalize = str => {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

/**
 * Converts an integer to a valid CSS hex value
 *
 * @param  {number} value
 * @return {string}
 */
export const intToHex = value => {
  const t = value.toString(16)
  return t.length < 6 ? '0'.repeat(6 - t.length) + t : t
}

/**
 * Converts a string hex to a valid integer
 *
 * @param  {string} value
 * @return {number}
 */
export const hexToInt = value => parseInt(value, 16)

/**
 * Converts a HEX string to a valid RGB
 *
 * @param  {[type]} value
 * @return {[type]}
 */
export const hexToRgb = value => {
  const hex = value.startsWith('#') ? value.substr(1,6) : value
  return {
    r: hexToInt(hex.substr(0, 2)),
    g: hexToInt(hex.substr(2, 2)),
    b: hexToInt(hex.substr(4, 2))
  }
}

/**
 * Converts an integer to a valid RGB
 *
 * @see {@link https://math.stackexchange.com/a/3343322 Stackexchange}
 *
 * @param  {number} C
 * @param  {boolean} inverse
 * @param  {boolean} toString
 * @return {object}
 */
export const intToRgb = (C, inverse, toString) => {
  const rgb = {
    r: inverse ? C & 0xff : (C >> 16) & 0xff,
    g: (C >> 8) & 0xff,
    b: inverse ? (C >> 16) & 0xff : C & 0xff
  }
  return toString ? `${rgb.r},${rgb.g},${rgb.b}` : rgb
}

/**
 * Checks wheter the given string URL is valid or not per RFC 3886. Note that
 * this checks if URL is well-formed and not if it does exist.
 *
 * @see {@link https://stackoverflow.com/a/43467144/2274773 Stackoverflow}
 * @see {@link https://www.rfc-editor.org/rfc/rfc3986       RFC 3886}
 * @param  {string} str
 * @return {boolean}
 */
export const isValidURL = (str) => {
  try {
    const url = new URL(str)
    return url.protocol === "http:" || url.protocol === "https:"
  } catch (ex) {
    return false
  }
}

/**
 * Gets the average brightness and detects wether a light or a dark font
 * must be used.
 *
 * @see {@link https://stackoverflow.com/a/57720742/2274773 Stackoverflow}
 * @param  {[type]} hex
 * @return {[type]}
 */
/*export const getBrightness = color => {
  return (color.r + color.g + color.b) / (255 * 3) >= 0.5 ? 'dark' : 'light'
}*/

/**
 * Gets the average brightness.
 *
 * @see {@link https://harthur.github.io/brain/ YIQ formula}
 * @see {@link https://github.com/bgrins/TinyColor/blob/master/tinycolor.js#L68 TinyColor plugin}
 * @param  {[type]} color
 * @return {[type]}
 */
export const getBrightness = color => {
  return (color.r * 299 + color.g * 587 + color.b * 114) / 1000
}

/**
 * Determines if color is dark.
 *
 * @param  {[type]} color
 * @return {[type]}
 */
export const isDark = (color) => {
  return getBrightness(color) < 128
}

/**
 * Determines if color is light.
 *
 * @param  {[type]} color
 * @return {[type]}
 */
export const isLight = (color) => {
  return !isDark(color)
}

/**
 * [description]
 *
 * @param  {[type]} _url
 * @param  {[type]} withoutQuery
 * @return {[type]}
 * @todo Change with pathname
 */
export const getUrl = (_url, withoutQuery) => {
  const url = new URL(_url.startsWith('/') ? window.location.origin + _url : _url)
  return withoutQuery ? url.origin + url.pathname : url.href
}

/**
 * [description]
 *
 * @param  {[type]} path
 * @param  {[type]} queryObject
 * @return {[type]}
 */
export const toUrl = (path, queryObject) => {
  const url = new URL(getUrl(path, true))
  if (queryObject) {
    Object.keys(queryObject)
      .forEach(key => url.searchParams.append(key, queryObject[key]))
  }
  return url
}

/**
 * [description]
 *
 * @param  {[type]} _url
 * @return {[type]}
 */
export const path = (_url) => toUrl(_url).pathname

/**
 * [description]
 *
 * @param  {[type]} type
 * @return {[type]}
 */
export const getToastIcon = type => {
  switch (type) {
    case 'info':
      return 'bi-info-circle'
    case 'success':
      return 'bi-check-circle'
    case 'warning':
      return 'bi-exclamation-circle'
    case 'danger':
      return 'bi-x-circle'
  }
}

export const Arr = {
  has (arr, arg) {
    return Array.isArray(arr) ?
      arr.includes(arg) :
      false
  },
  includesAll (arr, ...args) {
    return Array.isArray(arr) ?
      Array.from(args)
        .every(i => arr.includes(i)) :
        false
  },
  includesAny (arr, ...args) {
    return Array.isArray(arr) ?
      Array.from(args)
        .some(i => arr.includes(i)) :
        false
  }
}

/**
 * [$display description]
 *
 * @type {Object}
 */
class Display {
  smAndUp = false
  mdAndUp = false
  lgAndUp = false
  xlAndUp = false
  smAndDown = false
  mdAndDown = false
  lgAndDown = false
  xlAndDown = false
  xs = false
  sm = false
  md = false
  lg = false
  xl = false
  xxl = false
  breakpoints = {
    sm: 576,
    md: 768,
    lg: 992,
    xl: 1200,
    xxl: 1400
  }

  constructor (breakpoints) {
    this.breakpoints = {
      ...this.breakpoints,
      ...breakpoints
    }
  }

  matches (size, reverse, exact) {
    if (typeof window === 'undefined') return false
    return window.matchMedia(`(${reverse ? 'max' : 'min'}-width: ${reverse && exact ? size - 0.2 : size}px)`).matches
  }

  init () {
    this.update()
    if (typeof window === 'undefined') return
    window.addEventListener('resize', this.update.bind(this), { passive: true })
  }

  update () {
    this.smAndUp = this.matches(this.breakpoints.sm),
    this.mdAndUp = this.matches(this.breakpoints.md),
    this.lgAndUp = this.matches(this.breakpoints.lg),
    this.xlAndUp = this.matches(this.breakpoints.xl),
    // https://getbootstrap.com/docs/5.3/layout/breakpoints/#max-width
    this.smAndDown = this.matches(this.breakpoints.md, true, true),
    this.mdAndDown = this.matches(this.breakpoints.lg, true, true),
    this.lgAndDown = this.matches(this.breakpoints.xl, true, true),
    this.xlAndDown = this.matches(this.breakpoints.xxl, true, true),
    this.xs = this.matches(this.breakpoints.sm, true, true),
    this.sm = this.matches(this.breakpoints.sm) && this.matches(this.breakpoints.md, true, true),
    this.md = this.matches(this.breakpoints.md) && this.matches(this.breakpoints.lg, true, true),
    this.lg = this.matches(this.breakpoints.lg) && this.matches(this.breakpoints.xl, true, true),
    this.xl = this.matches(this.breakpoints.xl) && this.matches(this.breakpoints.xxl, true, true),
    this.xxl = this.matches(this.breakpoints.xxl)
  }
}

const install = (Vue) => {
  const utils = {
    $display: reactive(new Display()),
    formToObject,
    locale,
    capitalize,
    intToHex,
    hexToInt,
    hexToRgb,
    intToRgb,
    isValidURL,
    getBrightness,
    isDark,
    isLight,
    getUrl,
    getToastIcon,
    toUrl,
    Arr
  }
  utils.$display.init()
  // Inject a globally available $utils object
  Vue.config.globalProperties.$utils = utils
  // Also provide access to plugin via injection in component
  Vue.provide('utils', utils)

  // Vue.provide('virtualkeyboard', window.navigator.userAgent.includes('Electron'))
  // Vue.config.globalProperties.$virtualKeyboard = window.navigator.userAgent.includes('Electron')
}

export default {
  install
}
