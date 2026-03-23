#version 300 es
// =============================================================================
// ANCHOR:SHADER_ANTHILL_FRAG
// Description: Renders an ant hill as a circular dirt mound viewed from above.
//              Uses a smoothstep radial gradient from center to edge.
//              Colony color tints the center of the mound.
// Varyings (from a shared passthrough vert using u_worldToNDC):
//   v_world_pos - world-space pixel position
// Uniforms:
//   u_hill_center  - world-space center of the hill
//   u_hill_radius  - radius of the hill in world units
//   u_colony_color - RGB colony identifier color
// =============================================================================

precision mediump float;

in vec2 v_world_pos;

uniform vec2  u_hill_center;
uniform float u_hill_radius;
uniform vec3  u_colony_color;

out vec4 fragColor;

void main() {
    float dist = length(v_world_pos - u_hill_center);
    float t    = 1.0 - smoothstep(0.0, u_hill_radius, dist);

    if (t <= 0.001) discard;

    // Inner dirt: warm brown, lighter at center (raised mound effect)
    vec3 dirtDark  = vec3(0.18, 0.12, 0.06);
    vec3 dirtLight = vec3(0.42, 0.30, 0.16);
    vec3 dirt = mix(dirtDark, dirtLight, t * t);

    // Colony color blended at the very center (entrance marker)
    vec3 col = mix(dirt, u_colony_color * 0.7 + dirt * 0.3, t * t * 0.55);

    // Soft edge alpha
    float alpha = smoothstep(0.0, 0.15, t) * 0.92;

    fragColor = vec4(col, alpha);
}
